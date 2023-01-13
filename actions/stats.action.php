<?php
#
# This file is part of the STAR Shift Signup UI package
#
# This program is free software; you can redistribute it and/or modify it
# under the terms of the GNU General Public License as published by the
# Free Software Foundation; either version 2, or (at your option) any
# later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA
#

function stats_action() {

    $tpl =& Template::Instance();
    $cfg =& Config::Instance();
    $db  =& Db::Instance();

    // get number of distinct users signed for shifts
    $npeople = $db->Query('SELECT COUNT(DISTINCT(personID)) as cnt FROM `ShiftSignup`.Shifts');
    $npeople = $npeople['cnt'][0];

    $inst_ctrl = get_controls_institutions();
    $inst = array();
    foreach($inst_ctrl as $k => $v) {
	$inst[$v['id']] = $v;
    }

    $tshifts = $db->Query('SELECT * FROM `ShiftSignup`.Shifts'); // shifts taken

    $pswi       = explode(',', $cfg->Get('run', 'partial_shift_week_ids'        ));
    $nswi       = explode(',', $cfg->Get('run', 'noop_shift_week_ids'        ));
    $dtswi      = explode(',', $cfg->Get('run', 'disabled_training_shift_week_ids'));
    $nweeks     = intval($cfg->Get('run',       'number_of_weeks_total'         ));

    $run_start = $cfg->Get('run', 'run_start_date');
    $run_start_seconds = strtotime($run_start);
    $cur_time = time();
    $current_week = 0;
    for ($i = 0; $i < $nweeks; $i++) {
	    $tm_st = $run_start_seconds + $i * 7 * 24 * 3600;
    	if ($tm_st < $cur_time) { $current_week = $i; }
    }

    $shifts = array(); // shifts allowed
    for ($i = 0; $i < $nweeks; $i++) {
        for ($j = 0; $j < 3; $j++) {
            for ($k = 1; $k <= 8; $k++) {
				if ( in_array($i, $nswi) == true ) {
				    $shifts[$i][$j][$k] = -1;
				} else if ( in_array($i, $pswi) == true && $k >= 4 && $k <= 7) {
				    // partial week
				    $shifts[$i][$j][$k] = -1;
				} else if ( in_array($i, $dtswi) == true && $k >= 6 && $k <= 7 ) {
				    // disabled training week
				    $shifts[$i][$j][$k] = -1;
				} else {
				    // regular week
            	    $shifts[$i][$j][$k] = 0;
				}
            }
        }
    }


    $openQA = $nweeks - $cfg->Get('generic','offline_qa_delay');
    // fill shifts array with taken shifts, so "1" => taken, "0" => available, "-1" => not available
    if (!empty($tshifts)) {
	foreach($tshifts as $k => $v) {
	    $shifts[$v['week']][$v['shiftNumber']][$v['shiftTypeID']] = 1;	    
	    if ($v['shiftTypeID'] == 8) {
		$openQA -= 1;
	    };
	}
    }


    $openCT = 0; $openSL = 0; $openDO = 0; $openRT = 0; $openSLT = 0; $openDOT = 0;
    $totalQA = $nweeks - $cfg->Get('generic','offline_qa_delay') - count($nswi); 
    $totalCT = $nweeks;
	$totalSL = $nweeks * 3;
	$totalDO = $nweeks * 6;
	$totalRT = $nweeks * 3; 
	$totalSLT = $nweeks * 3;
	$totalDOT = $nweeks * 3;

    for ($i = 0; $i < $nweeks; $i++) {
        for ($j = 0; $j < 3; $j++) {
                if ($shifts[$i][$j][2] == 0) { $openSL += 1; }
                if ($shifts[$i][$j][3] == 0) { $openDO += 1; }
				if ($shifts[$i][$j][4] == 0) { $openDO += 1; }
                if ($shifts[$i][$j][5] == 0) { $openRT += 1; }
                if ($shifts[$i][$j][6] == 0) { $openSLT += 1; }
                if ($shifts[$i][$j][7] == 0) { $openDOT += 1; }

                if ($shifts[$i][$j][2] == -1) { $totalSL -= 1; }
                if ($shifts[$i][$j][3] == -1) { $totalDO -= 1; }
				if ($shifts[$i][$j][4] == -1) { $totalDO -= 1; }
                if ($shifts[$i][$j][5] == -1) { $totalRT -= 1; }
                if ($shifts[$i][$j][6] == -1) { $totalSLT -= 1; }
                if ($shifts[$i][$j][7] == -1) { $totalDOT -= 1; }
        }
		if ($shifts[$i][0][1] == 0) { $openCT += 1; }
		if ($shifts[$i][0][1] == -1) { $totalCT -= 1; }
    }

    $perCT = round($openCT/$totalCT*100.0, 1);
    $perDO = round($openDO/$totalDO*100.0, 1);
    $perSL = round($openSL/$totalSL*100.0, 1);
    $perRT = round($openRT/$totalRT*100.0, 1);
    $perDOT = round($openDOT/$totalDOT*100.0, 1);
    $perSLT = round($openSLT/$totalSLT*100.0, 1);
    $perQA = round($openQA/$totalQA*100.0, 1);

    // process form data
    if ( !empty( $_POST['submit'] ) ) {
	$instID = intval($_POST['inst_name']);
	$showExperts = $_POST['show_experts'];
	$hasNoShifts = $_POST['has_no_shifts'];
	$showAuthorsOnly = $_POST['show_authors_only'];
	$suppressStats = $_POST['suppress_stats'];
	$kShiftType[0] = $_POST['shift_type'][0];
	$kShiftType[1] = $_POST['shift_type'][1];
	$kShiftType[2] = $_POST['shift_type'][2];
	$kShiftType[3] = $_POST['shift_type'][3];
	$kShiftType[4] = $_POST['shift_type'][4];
	$kShiftType[5] = $_POST['shift_type'][5];
	
	$members = array();
/*
	$mem = pnb_get_active_members();
	foreach($mem as $k => $v) {
	    if ( $instID > 0 && $v['fields'][17] != $instID ) { continue; }
	    if ( $showExperts != 'on' && $v['fields'][43] == 'y' ) { continue; }
	    if ( $showAuthorsOnly == 'on' && $v['fields'][40] == 'n' ) { continue; }
	    if ( $v['fields'][40] != 'y' || $v['fields'][42] != 'y' ) { continue; }
	    $members[$k] = array( 'Id' => $k, 'FirstName' => $v['fields'][1], 'LastName' => $v['fields'][3],
        	'isExpert' => $v['fields'][43], 'Expertise' => $v['fields'][45], 'Phone' => $v['fields'][22], 'CellPhone' => $v['fields'][23],
        	'BnlPhone' => $v['fields'][32], 'EmailAddress' => $mem[$k]['fields'][20]
	    );
	}
*/

	$mem = get_controls_members();

//	print_r($mem); exit;

	foreach ($mem as $k => $v ) {
	    if ( $instID != -1 && $v['InstitutionId'] !== $instID ) { continue; }
	    // if ( $v['isShifter'] == 'n' ) { continue; } // SPHENIX DOES NOT USE is_shifter flag!
	    if ( $showAuthorsOnly == 'on' && $v['isAuthor'] == 'n' ) { continue; }
	    if ( $showExperts != 'on' && $v['isExpert'] != 'n' ) { continue; }
	    $members[$v['Id']] = $v;
	}

//	echo $instID;
//	print_r($members); exit;

	/*
	$sql = 'SELECT Id, FirstName, LastName, Phone, EmailAddress, isExpert, Expertise FROM `starweb`.members WHERE ';
	if ($instID == -1 || $instID == 0) {
	    // all institutions, no need for constrain
	} else {
    	    $sql .= 'institutionId = '.$instID.' AND';
	}
	if ($showExperts != 'on') {
	    $sql .= ' isExpert != "Y" AND';
	}
	$sql .= ' isAuthor = "y" AND isShifter = "y" AND LeaveDate > NOW()';
	$sql .= ' ORDER BY LastName';
	$mem = $db->Query($sql);
	$members = array();
	foreach($mem as $k => $v) {
    	    if (!empty($v['Id'])) {
        	$members[$v['Id']] = $v;
    	    }
        }
	unset($mem);
	*/

	$ids = array_keys($members);
	$ids_str = implode(',', $ids);
	$kTotalMembers = count($ids);

	$sql = 'SELECT * FROM `ShiftSignup`.ShiftTraining WHERE personID IN ( '.$ids_str.' )';
	$trn = $db->Query($sql);

  // SPHENIX FIX: all people are considered trained:
  $training_cfg = $cfg->Get('generic', 'consider_everyone_trained');
  if ( $training_cfg ) {
		foreach ( $ids as $k => $v ) {
			$trn[] = [ 'id' => 1, 'shiftTypeID' => 1, 'personID' => $v, 'beginTime' => '2000-01-01 00:00:00', 'isPending' => 0 ];
			$trn[] = [ 'id' => 1, 'shiftTypeID' => 2, 'personID' => $v, 'beginTime' => '2000-01-01 00:00:00', 'isPending' => 0 ];
			$trn[] = [ 'id' => 1, 'shiftTypeID' => 3, 'personID' => $v, 'beginTime' => '2000-01-01 00:00:00', 'isPending' => 0 ];
			$trn[] = [ 'id' => 1, 'shiftTypeID' => 4, 'personID' => $v, 'beginTime' => '2000-01-01 00:00:00', 'isPending' => 0 ];
		}
  }

	foreach($trn as $k => $v) {
	    if (!empty($v['personID'])) {
    		$members[$v['personID']]['training'][$v['shiftTypeID']] = $v;
	    }
	}

	$sql = 'SELECT * FROM `ShiftSignup`.Shifts WHERE personID IN ( '.$ids_str.' )';
	$shf = $db->Query($sql);
	foreach($shf as $k => $v) {
	    if (!empty($v['personID'])) {
    		$members[$v['personID']]['shiftstaken'][$v['shiftTypeID']] = $v;

			// counter of processed / not processed shifts
			if ( !isset( $members[$v['personID']]['total_shifts_signed'] ) ) {
				$members[$v['personID']]['total_shifts_signed'] = 0;
				$members[$v['personID']]['shifts_processed'] = 0;
				$members[$v['personID']]['shifts_unprocessed'] = 0;
			}
			$members[$v['personID']]['total_shifts_signed'] += 1;
			if ( $v['week'] <= $current_week ) {
				$members[$v['personID']]['shifts_processed'] += 1;
			} else {
				$members[$v['personID']]['shifts_unprocessed'] += 1;
			}

	    }	    
	}

/*
	// past data retrieval START
	    $db1 =& Db::Instance('db_past_1');
	    $db2 =& Db::Instance('db_past_2');
	    $db3 =& Db::Instance('db_past_3');
	    $dbs = array($db1, $db2, $db3);
	    foreach($dbs as $past_db_key => $past_db) {
		$sql = 'SELECT * FROM `ShiftSignup`.Shifts WHERE personID IN ( '.$ids_str.' )';
    		$pastshf = $past_db->Query($sql);
	        foreach($pastshf as $k => $v) {
    		    if (!empty($v['personID'])) {
			if (!isset($members[$v['personID']]['past_shiftstaken'])) { $members[$v['personID']]['past_shiftstaken'] = array(); }
			if (!isset($members[$v['personID']]['past_shiftstaken'][$past_db_key])) { $members[$v['personID']]['past_shiftstaken'][$past_db_key] = 0; }
            		$members[$v['personID']]['past_shiftstaken'][$past_db_key] += 1;
        	    }
    		}
	    }
	// past data retrieval END
*/

	$canSL = array();
	$canDO = array();
	$canRT = array();
	$canSLT = array();
	$canDOT = array();
	$canQA = array();

        foreach ($members as $k => $v) {
    	    if ($hasNoShifts) {
    		if (!isset($v['shiftstaken'])) {
        	    if ( isset($v['training'][2]) ) {
            		$canSL[] = $v;
        	    }
        	    if ( isset($v['training'][3]) ) {
            		$canDO[] = $v;
        	    }
        	    $canRT[] = $v;
        	    if ( isset($v['training'][4]) ) {
            		$canQA[] = $v;
        	    }
        	    if ( !isset($v['training'][2]) ) {
            		$canSLT[] = $v;
        	    }
        	    if ( !isset($v['training'][3]) ) {
            		$canDOT[] = $v;
        	    }
    		}
    	    } else {
        	if ( isset($v['training'][2]) ) {
            	    if (isset($v['shiftstaken'])) {
                	$v['LastName'] = '<font color="blue">'.$v['LastName'].'</font>';
                	$v['FirstName'] = '<font color="blue">'.$v['FirstName'].'</font>';
            	    }
            	    $canSL[] = $v;
        	}
        	if ( isset($v['training'][3]) ) {
            	    if (isset($v['shiftstaken'])) {
                	$v['LastName'] = '<font color="blue">'.$v['LastName'].'</font>';
                	$v['FirstName'] = '<font color="blue">'.$v['FirstName'].'</font>';
            	    }
            	    $canDO[] = $v;
        	}
        	if ( isset($v['training'][4]) ) {
            	    if (isset($v['shiftstaken'])) {
                	$v['LastName'] = '<font color="blue">'.$v['LastName'].'</font>';
                	$v['FirstName'] = '<font color="blue">'.$v['FirstName'].'</font>';
            	    }
            	    $canQA[] = $v;
        	}
        	if ( !isset($v['training'][2]) ) {
            	    if (isset($v['shiftstaken'])) {
                	$v['LastName'] = '<font color="blue">'.$v['LastName'].'</font>';
                	$v['FirstName'] = '<font color="blue">'.$v['FirstName'].'</font>';
            	    }
            	    $canSLT[] = $v;
        	}
        	if ( !isset($v['training'][3]) ) {
            	    if (isset($v['shiftstaken'])) {
                	$v['LastName'] = '<font color="blue">'.$v['LastName'].'</font>';
                	$v['FirstName'] = '<font color="blue">'.$v['FirstName'].'</font>';
            	    }
            	    $canDOT[] = $v;
        	}
        	if (isset($v['shiftstaken'])) {
            	    $v['LastName'] = '<font color="blue">'.$v['LastName'].'</font>';
            	    $v['FirstName'] = '<font color="blue">'.$v['FirstName'].'</font>';
        	}
        	$canRT[] = $v;
    	    }
	}
	
	
	$tpl->set('members', $members);
	$tpl->set('inst_selected', $instID);
	$tpl->set('kShiftType', $kShiftType);
	$tpl->set('hasNoShifts', $hasNoShifts);
	$tpl->set('showAuthorsOnly', $showAuthorsOnly);
	$tpl->set('suppressStats', $suppressStats);

	$tpl->set('canSL', $canSL);
	$tpl->set('canDO', $canDO);
	$tpl->set('canRT', $canRT);
	$tpl->set('canSLT', $canSLT);
	$tpl->set('canDOT', $canDOT);
	$tpl->set('canQA', $canQA);
	$tpl->set('showExperts', $showExperts);
    } else {

    $kShiftType = array();
    $kShiftType[0] = 1;
    $kShiftType[1] = 1;
    $kShiftType[2] = 1;
    $tpl->set('kShiftType', $kShiftType);
    $tpl->set('hasNoShifts', 1);

    }

    $tpl->set('openCT', $openCT);
    $tpl->set('openSL', $openSL);
    $tpl->set('openDO', $openDO);
    $tpl->set('openRT', $openRT);
    $tpl->set('openSLT', $openSLT);
    $tpl->set('openDOT', $openDOT);
    $tpl->set('openQA', $openQA);

    $tpl->set('totalCT', $totalCT);
    $tpl->set('totalSL', $totalSL);
    $tpl->set('totalDO', $totalDO);
    $tpl->set('totalRT', $totalRT);
    $tpl->set('totalSLT', $totalSLT);
    $tpl->set('totalDOT', $totalDOT);
    $tpl->set('totalQA', $totalQA);

    $tpl->set('perCT', $perCT);
    $tpl->set('perSL', $perSL);
    $tpl->set('perDO', $perDO);
    $tpl->set('perRT', $perRT);
    $tpl->set('perDOT', $perDOT);
    $tpl->set('perSLT', $perSLT);
    $tpl->set('perQA', $perQA);

//    $tpl->set('openSumNT', $openCT + $openSL + $openDO + $openRT + $openQA);
//    $tpl->set('totalSumNT', $totalCT + $totalSL + $totalDO + $totalRT + $totalQA);
//    $tpl->set('openSum', $openCT + $openSL + $openDO + $openRT + $openQA + $openSLT + $openDOT);
//    $tpl->set('totalSum', $totalCT + $totalSL + $totalDO + $totalRT + $totalQA + $totalSLT + $totalDOT);

    $tpl->set('openSumNT', $openCT + $openSL + $openDO + $openRT);
    $tpl->set('totalSumNT', $totalCT + $totalSL + $totalDO + $totalRT);
    $tpl->set('openSum', $openCT + $openSL + $openDO + $openRT + $openSLT + $openDOT);
    $tpl->set('totalSum', $totalCT + $totalSL + $totalDO + $totalRT + $totalSLT + $totalDOT);

    $tpl->set('inst', $inst);
    $tpl->set('npeople', $npeople);
    $tpl->set_file('stats.tpl.php');
}
