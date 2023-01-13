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

function shifttable_action() {

    $institution = $_GET['sel1']; // strip 'i_'
    $person = intval($_GET['sel2']);

    $tpl =& Template::Instance();    
    $cfg =& Config::Instance();
    $db  =& Db::Instance();

    $unthresh = $cfg->Get('generic', 'institution_unassign_threshold');

    $run_start_date = $cfg->Get('run', 'run_start_date');
    $nweeks = intval($cfg->Get('run','number_of_weeks_total'));

    $pswi  = explode(',', $cfg->Get('run', 'partial_shift_week_ids'));
	$nswi  = explode(',', $cfg->Get('run', 'noop_shift_week_ids'));
    $dtswi = explode(',', $cfg->Get('run', 'disabled_training_shift_week_ids'));

    /*
    $members = $db->Query('SELECT m.Id, m.FirstName, m.LastName, m.UnicodeName, m.InstitutionId, i.InstitutionName FROM `starweb`.members m, 
	`starweb`.institutions i WHERE m.InstitutionId = i.Id AND m.InstitutionId > 0 AND m.isShifter = "Y" ORDER BY m.InstitutionId, m.LastName');

    $mem = array();
    foreach($members as $k => $v) {
	$mem[$v['Id']] = $v;	
    }
    unset($members);
    */

    $mem = get_shifttable_members();

    $person_trainings = array(); $ptrain = array();
    if ( $person > 0 ) {
			$person_trainings = $db->Query('SELECT id, shiftTypeID, personID, beginTime, isPending FROM `ShiftSignup`.ShiftTraining WHERE personID = '.$person);

			// SPHENIX FIX: all people are considered trained:
			$training_cfg = $cfg->Get('generic', 'consider_everyone_trained');
			if ( $training_cfg ) {
				$person_trainings = [
					[ 'id' => 1, 'shiftTypeID' => 1, 'personID' => $person, 'beginTime' => '2000-01-01 00:00:00', 'isPending' => 0 ],
					[ 'id' => 1, 'shiftTypeID' => 2, 'personID' => $person, 'beginTime' => '2000-01-01 00:00:00', 'isPending' => 0 ],
					[ 'id' => 1, 'shiftTypeID' => 3, 'personID' => $person, 'beginTime' => '2000-01-01 00:00:00', 'isPending' => 0 ],
					[ 'id' => 1, 'shiftTypeID' => 4, 'personID' => $person, 'beginTime' => '2000-01-01 00:00:00', 'isPending' => 0 ]
				];
			}
    }
    
    // timing array, each entry is a start time of week, in unixtime (seconds)..
    $cur_time = time();
    $weeks = array();
    $tm = strtotime($run_start_date);
    $current_week = -1;
    $current_week_start = -1;
    $override = $cfg->Get('generic', 'override_subscription_limits');

    for ($i = 0; $i < $nweeks; $i++) {
			$sec = $tm + $i * 7 * 24 * 3600;
			$val = array('start' => $sec, 'date' => date('r', $sec));
			if (($cur_time > $sec) && empty($override) ) {
	    	$val['lock'] = 1;
			}
			$weeks[] = $val;
			if ($sec < $cur_time) { $current_week = $i; $current_week_start = $sec - 1; }
		}

	if (is_array($person_trainings)) {
    foreach($person_trainings as $k => $v) {
			$ptrain[$v['shiftTypeID']]['trainings'] = $v;
			if ( $cfg->Get('run', 'disable_period_coordinator_signup') == true && $v['shiftTypeID'] == 1) {
	    	continue;
			}
			$train_time = strtotime($v['beginTime']);
			foreach( $weeks as $wk => $wv ) {
	    	if ($train_time < $wv['start']) {
					$ptrain[$v['shiftTypeID']]['allowed_from'] = $wk; ; // allowed to take [Crd|ShL|DetOp] shifts starting from week X
					if ( $cfg->Get('run', 'relax_period_coordinator_training') == true ) {
		    		if ( $v['shiftTypeID'] == 2 || $v['shiftTypeID'] == 3 ) {
							$ptrain[1]['allowed_from'] = $wk;
		    		}
					}
					break;
	    	}
			}	
  	}
  }

    $shifts = $db->Query('SELECT * FROM ShiftSignup.Shifts');
    // print_r($shifts);exit;
    $sharr = array();
    $taken_by_person = array();
    if (is_array($shifts)) {
    foreach($shifts as $k => $v) {
	$sharr[$v['week']][$v['shiftNumber']][$v['shiftTypeID']] = $v;
	// here we know $v['personID'] and $v['shiftTypeID']
	// need to check if shiftTypeID == 2 and leader_ok == 0 => report!
	// need to check if shiftTypeID > 2 && shiftTypeID <= 7 and leader_ok == 0 => report!

	// FIXME:

	// end of training check..
	if ($v['personID'] == $person) {
	    $taken_by_person[$v['week']] = 1;
	    if ( ($v['shiftTypeID'] == 3 || $v['shiftTypeID'] == 4) && !empty($ptrain[3]) ) {
		$ptrain[3]['locked'] = 1;
	    }
	    if ( ($v['shiftTypeID'] == 2) && !empty($ptrain[2]) ) {
		$ptrain[2]['locked'] = 1;
	    }
	}
    }
    }
    unset($shifts);

    if (is_array($nswi)) {
    	foreach($nswi as $k => $v) {
			for ($j = 0; $j < 3; $j++) {
				for ($i = 0; $i < 8; $i++) {
	    			$sharr[$v][$j][$i] = 'N/A';
				}
			}
    	}
	}

    if (is_array($pswi)) {
    foreach($pswi as $k => $v) {
	for ($j = 0; $j < 3; $j++) {
	for ($i = 4; $i < 8; $i++) {
	    $sharr[$v][$j][$i] = 'N/A';
	}
	}
    }
//	print_r($sharr);
    }
    if (is_array($dtswi)) {
    foreach($dtswi as $k => $v) {
	for ($j = 0; $j < 3; $j++) {
	for ($i = 6; $i < 8; $i++) {
	    $sharr[$v][$j][$i] = 'N/A';
	}
	}
    }
    }

    
    // Vacated slots highlighted in red:
    $shifts_vacated = $db->Query('SELECT id, week_id, shift_slot_id, shift_type_id, inst_name FROM `ShiftSignup`.ShiftActions WHERE action_type = "removed" and shift_slot_id != 0 and shift_type_id < 6 and week_id > '.$current_week.' AND week_id <= '.($current_week+$unthresh).' ORDER BY action_timestamp ASC');
    $shifts_vac = array();
    if (!empty($shifts_vacated) && is_array($shifts_vacated)) {
	foreach($shifts_vacated as $k => $v) {
	    $shifts_vac[$v['week_id']][$v['shift_slot_id']][$v['shift_type_id']] = 'Vacated by '.$v['inst_name'];
	}
    } 
    

//    print_r($weeks);
//    echo "\n".'-------------------------------'."\n";
//    print_r($ptrain);
//    exit;

//    print_r($taken_by_person);
//    exit;

    $tpl->set('show_member_url', $cfg->Get('generic', 'show_member_url'));
    $tpl->set('mem', $mem);
    $tpl->set('sharr', $sharr);
    $tpl->set('shvac', $shifts_vac);
    $tpl->set('pid', $person);
    $tpl->set('weeks', $weeks);
    $tpl->set('current_week', $current_week);
    $tpl->set('personName', $mem[$person]['FirstName'].' '.$mem[$person]['LastName']);
    $tpl->set('institutionID', $mem[$person]['InstitutionId']);
    $tpl->set('institutionName', $mem[$person]['InstitutionName']);
    $tpl->set('taken', $taken_by_person);
    $tpl->set('ptrain', $ptrain);
    $tpl->set('num_weeks_total', $nweeks);
    $tpl->set('run_start_date', $run_start_date);
    $tpl->set('reduced', intval($_GET['reduced']));
    $tpl->set_file('shifttable.tpl.php');
}