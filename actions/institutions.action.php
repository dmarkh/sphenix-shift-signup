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

function institutions_action() {

    $tpl =& Template::Instance();    
    $cfg =& Config::Instance();
    $db  =& Db::Instance();

    // count all required shifts from ShiftsAdmin table
    $req = $db->Query('SELECT SUM(shifts_required) AS req FROM `ShiftSignup`.`ShiftAdmin`');

    // count taken shifts from Shifts table
    $tkn_norm = $db->Query('SELECT COUNT(*) AS tkn FROM `ShiftSignup`.`Shifts` WHERE shiftTypeID NOT IN (6,7,8)');
    $tkn_trn = $db->Query('SELECT COUNT(*) AS tkn FROM `ShiftSignup`.`Shifts` WHERE  shiftTypeID IN (6,7)');    
    $tkn_oflqa = $db->Query('SELECT COUNT(*) AS tkn FROM `ShiftSignup`.`Shifts` WHERE  shiftTypeID = 8');

    // total number of shifts taken :
    $tkn_total = $tkn_norm['tkn'][0] + $tkn_trn['tkn'][0] + $tkn['oflqa'][0];

    /*
    // get all alive institutions
    $in = $db->Query('SELECT Id AS id, InstitutionName AS name FROM `starweb`.institutions WHERE ISNULL(LeaveDate) = true OR LeaveDate > NOW() OR LeaveDate = 0 ORDER BY InstitutionName ASC');
    $inst = array();
    foreach($in as $k => $v) {
	$inst[$v['id']]['name'] = $v['name'];
    }
    unset($in);
    */

    $experts = get_expert_members();                                                                                                                                    
    //print_r($experts); exit;

    $inst_expertcredits = array();                                                                                                                                      
    foreach($experts as $k => $v) {                                                                                                                                     
        if ( !empty($v['ExpertCredit']) ) { $inst_expertcredits[$v['InstitutionId']] += intval($v['ExpertCredit']); }                                                   
    }

    $inst = get_inst_institutions();

	foreach($inst as $k => $v ) {
		$inst[$k]['active'] = [];
		$inst[$k]['passive'] = [];
		$inst[$k]['experts'] = [];
		$inst[$k]['experts_nonauth'] = [];
		$inst[$k]['notauthor'] = [];
	}

    $inst_keys = implode(',',array_keys($inst));

    $taken = $db->Query('SELECT institution_id, COUNT(*) AS tkn FROM `ShiftSignup`.Shifts WHERE personID > 0 GROUP BY institution_id');
    foreach($taken as $k => $v) {
		$inst[$v['institution_id']]['staken'] = $v['tkn'];
    }

		if ( $cfg->Get('run','deduct_parallel_training_credits') == 1 ) {
			// Dmitry: training fix for parallel trainings
			// Dmitry: total = N total shifts - N training shifts taken on the same week as regular shift
			$trn_correction = [];
			$trn_inst_correction = [];
			$res = $db->Query('SELECT shiftNumber, shiftTypeID, week, personID, institution_id FROM `ShiftSignup`.Shifts ORDER BY personID ASC, week ASC, shiftTypeID ASC');
			foreach( $res as $k => $v ) {
				if ( !isset( $trn_correction[ $v['institution_id'] ] ) ) {
					$trn_correction[ $v['institution_id'] ] = [];
				}
				if ( !isset( $trn_correction[ $v['institution_id'] ][ $v['personID'] ] ) ) {
					$trn_correction[ $v['institution_id'] ][ $v['personID'] ] = [];
				}
				if ( !isset( $trn_correction[ $v['institution_id'] ][ $v['personID'] ][ $v['week'] ] ) ) {
					$trn_correction[ $v['institution_id'] ][ $v['personID'] ][ $v['week'] ] = [ 'reg' => 0, 'trn' => 0 ];
				}
				if ( ( $v['shiftTypeID'] >= 0 && $v['shiftTypeID'] <= 5 ) || $v['shiftTypeID'] == 8 ) {
					$trn_correction[ $v['institution_id'] ][ $v['personID'] ][ $v['week'] ][ 'reg' ] += 1;
				} else {
					$trn_correction[ $v['institution_id'] ][ $v['personID'] ][ $v['week'] ][ 'trn' ] += 1;
				}
			}
			// okay got stats
			foreach( $trn_correction as $inst_k => $inst_v ) {
				foreach( $inst_v as $mem_k => $mem_v ) {
					foreach( $mem_v as $week_k => $week_v ) {
						if ( isset($week_v['reg']) && isset($week_v['trn']) && $week_v['reg'] > 0 && $week_v['trn'] > 0 ) {
							if ( !isset($trn_inst_correction[ $inst_k ]) ) {
								$trn_inst_correction[ $inst_k ] = 0;
							}
							$trn_inst_correction[ $inst_k ] += $week_v['trn'];
						}
 					}
				}
			}
			foreach( $trn_inst_correction as $k => $v ) {
				if ( isset( $inst[ $k ] ) && $inst[$k]['staken'] > 0 ) {
					$inst[$k]['staken'] -= $v;
				}
			}
		} // Dmitry: training fix for parallel trainings


    $eff = $db->Query('SELECT * from `ShiftSignup`.ShiftAdmin WHERE institution_id IN ('.$inst_keys.')');
    $shifts_required = 0; 
    $need_past_years = intval($cfg->Get('run','accounting_use_past_years'));
    foreach($eff as $k => $v) {
		$inst[$v['institution_id']]['effective_authors'] = intval($v['effective_authors']);
		//$inst[$v['institution_id']]['detector_experts'] = intval($v['detector_experts']);
		$inst[$v['institution_id']]['detector_experts'] = intval( $inst_expertcredits[$v['institution_id']] );
		$inst[$v['institution_id']]['shifts_required'] = intval($v['shifts_required']);
		if ( intval($v['shifts_extra']) < 0 ) {
		    $inst[$v['institution_id']]['staken'] -= intval($v['shifts_extra']);
		};
		$shifts_required += intval($v['shifts_required']);
		// add historical data if requested by config variable
		if ( $need_past_years > 0 ) {
			$tmp = explode(';', $v['historical_data']);
			$tmp = explode(';', trim($v['historical_data'], "; \n\t\r"));
			for ( $year = 0; $year < $need_past_years; $year++ ) {
    				$tmp2 = explode(',',$tmp[$year]); // [ required, taken ]
				// $shifts_required += intval($tmp2[0]);
				$inst[$v['institution_id']]['shifts_required'] += intval($tmp2[0]);
				$inst[$v['institution_id']]['staken'] += intval($tmp2[1]);
			}
		}
    }

    /*
    // for each institution we should select 1), active authors 2) ex-authors, 3) non-authors.
    $m = $db->Query('SELECT Id, InstitutionId, FirstName, LastName, isAuthor, isExpert, Expertise, isEmeritus, DisabledDate,
	 UNIX_TIMESTAMP(NOW()) as tsnow, UNIX_TIMESTAMP(LeaveDate) as tsld, (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(LeaveDate)) as tsdiff,
	 DATEDIFF(NOW(), DisabledDate) as disdiff FROM `starweb`.members WHERE InstitutionId IN ('.$inst_keys.') ORDER BY LastName ASC');
    */

    $m = get_inst_members();

    foreach($m as $k => $v) {
	//if (strtolower($v['isAuthor']) == 'y' && ($v['DisabledDate'] == '0000-00-00 00:00:00' || $v['disdiff'] < 14) ) {
	if ( strtolower($v['isAuthor']) == 'y' ) {
	    // active author?
	    if ( empty($v['tsdiff']) ) {
		// yes, active
		if (strtolower($v['isExpert']) == 'y') {
		    $inst[$v['InstitutionId']]['experts'][] = '<a href="javascript:void(0);" onmouseover="return overlib(\'Expert: '.$v['Expertise'].'<br>Expert Credit: '.$v['ExpertCredit'].'\');" onmouseout="return nd();"><b>'.$v['FirstName'].' '.$v['LastName'].'</b></a>';
		} else if (strtolower($v['isEmeritus']) == 'y') {
		    // will save as underline author - makes no sens of not an author
		    $inst[$v['InstitutionId']]['active'][] = '<u>'.$v['FirstName'].' '.$v['LastName'].'</u>';
		} else {
		    $inst[$v['InstitutionId']]['active'][] = $v['FirstName'].' '.$v['LastName'];
		}
	    } else if ( !empty($v['tsdiff']) && $v['tsdiff'] < (365 * 24 * 3600) ) {
		// left collaboration, but still in author list
			$inst[$v['InstitutionId']]['passive'][] = $v['FirstName'].' '.$v['LastName'];
	    } else {
			//echo 'author, but left long ago';
	    }
	} else {
	    if ( empty($v['tsdiff']) ) {
		if (strtolower($v['isExpert']) == 'y') {
		    $inst[$v['InstitutionId']]['experts_nonauth'][] = '<a style="color: black;" href="javascript:void(0);" onmouseover="return overlib(\'Expert: '.$v['Expertise'].'<br>Expert Credit: '.$v['ExpertCredit'].'\');" onmouseout="return nd();"><b>'.$v['FirstName'].' '.$v['LastName'].'</b></a>';
		} else {
		    // valid, not author
		    $inst[$v['InstitutionId']]['notauthor'][] = $v['FirstName'].' '.$v['LastName'];
		}
	    } else {
		// echo 'non-author, left';
	    }
	}
    }

    $pswi       = explode(',', $cfg->Get('run', 'partial_shift_week_ids'        ));
    $nswi       = explode(',', $cfg->Get('run', 'noop_shift_week_ids'        ));
    $dtswi      = explode(',', $cfg->Get('run', 'disabled_training_shift_week_ids'));
    $nweeks     = intval($cfg->Get('run',       'number_of_weeks_total'         ));
    $spw        = intval($cfg->Get('generic',   'slots_per_week'                ));
    $sppw       = intval($cfg->Get('generic',   'slots_per_partial_week'        ));
    $spdtw      = intval($cfg->Get('generic',   'slots_per_disabled_training_week'));
    $oqd        = intval($cfg->Get('generic',   'offline_qa_delay'              ));

    // max = normal weeks + partial weeks + no training weeks + ofline qa weeks
    $max = ($nweeks - count($pswi) - count($dtswi) - count($nswi)) * $spw + count($pswi) * $sppw + count($dtswi) * $spdtw + ($nweeks - $oqd - count($nswi));

    $trainee_max = ($nweeks - count($pswi) - count($dtswi) - count($nswi)) * 6;

    $tpl->set('shifts_experts', $experts);
    $tpl->set('shifts_available', $max);
    $tpl->set('shifts_taken', $tkn_total);
    $tpl->set('shifts_required', $shifts_required);
    $tpl->set('shifts_qa', $nweeks - $oqd );
    $tpl->set('shifts_non_trainee', $max - $trainee_max );

    $tpl->set('shifts_taken_trainee', $tkn_trn['tkn'][0]);
    $tpl->set('shifts_trainee_max', $trainee_max);

    $tpl->set('slots_per_week', $spw);
    $tpl->set('total_weeks', $nweeks);

    if (isset($inst[0])) { unset($inst[0]); }
    $tpl->set('inst', $inst);

    $tpl->set('num_weeks_total', $cfg->Get('run','number_of_weeks_total'));
    $tpl->set_file('institutions.tpl.php');
}