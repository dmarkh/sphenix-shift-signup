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

function finalizesignup_action() {

    $person = intval($_POST['personID']);
    $personName = $_POST['personName'];
    $institutionID = intval($_POST['institutionID']);
    $institutionName = $_POST['institutionName'];

    $signupType = intval($_POST['signupType']); // 1 = generic, 2 = offline qa, 3 = spin?


    $tpl =& Template::Instance();    
    $cfg =& Config::Instance();
    $db  =& Db::Instance();

    $tm = strtotime($cfg->Get('run', 'run_start_date'));
    $nweeks = intval($cfg->Get('run', 'number_of_weeks_total'));

    // shift weeks :
    $shift_weeks = array();
    for ($i = 0; $i < $nweeks; $i++) {
 	$tm_st = $tm + $i * 7 * 24 * 3600;
	$shift_weeks[$i]['start'] = $tm_st;
    }

    $wk = $_POST['week']; // -123, or 345

    $weeks = array();

    if (empty($_POST['cancel'])) { // guard against cancel

    foreach($wk as $k => $v) {
	$weeks[$k]['value'] = $v;
	$shift_hours = abs(intval($v % 10));
	$shift_type = abs(intval($v/100));
 	$tm_st = $tm + $k * 7 * 24 * 3600;
	$tm_en = $tm + ($k+1) * 7 * 24 * 3600;
	$weeks[$k]['dates'] = date('M j', $tm_st).'<small>'.date('S', $tm_st).'</small> - '.date('M j', $tm_en).'<small>'.date('S',$tm_en).'</small>';
	$weeks[$k]['times'] = get_shift_hours($shift_hours);
	$weeks[$k]['shiftType'] = get_shift_type($shift_type);
	if (intval($v) > 0) { 
	    // insert new entry
	    
	    // check oversubscription protection, if not set => bypass trigger
	    $over_key = ', bypass';
	    $over_val = ', 1';
	    $protection = $cfg->Get('generic','oversubscription_protection');
	    if (!empty($protection)) {
		$over_key = '';
		$over_val = '';
	    }
	    
	    $sql = 'INSERT INTO `ShiftSignup`.Shifts (beginTime, endTime, shiftNumber, shiftTypeID, personID, institution, week, institution_id '.$over_key.')';
	    $sql .= ' VALUES (FROM_UNIXTIME('.$tm_st.'), FROM_UNIXTIME('.$tm_en.'), '.$shift_hours.','.$shift_type.','.$person.',"'.$institutionName.'",'.$k.','.$institutionID.' '.$over_val.')';
	    $db->Query($sql);
	    if (!$db->IsError()) {
		$weeks[$k]['operation'] = '<font color="green">Added</font>';

		$tmp = $cfg->Get('access', 'log_user_actions');
		if (!empty($tmp)) {
                    $origin_ip    = get_ip_address();                                                                                         
                    $origin_host  = $_SERVER['REMOTE_HOST'];                                                                                         
                    if (empty($origin_host)) {                                                                                                       
                        $origin_host = gethostbyaddr($origin_ip);                                                                                    
                    }                                                                                                                                
                    $origin_agent = $_SERVER['HTTP_USER_AGENT'];                                                                                     
                                                                                                                                                     
                    $sql = 'INSERT INTO `ShiftSignup`.`ShiftActions` (action_type, origin_ip, origin_host, origin_agent, '                           
                        . 'user_id, user_name, inst_id, inst_name, '                                                                                 
                        . 'week_id, week_name, shift_slot, shift_type, shift_slot_id, shift_type_id) VALUES ("added", '                                                          
                        . '"'.$db->Escape($origin_ip).'", '                                                                                          
                        . '"'.$db->Escape($origin_host).'", '                                                                                        
                        . '"'.$db->Escape($origin_agent).'", '                                                                                       
                        . $person . ', '                                                                                                             
                        . '"'.$db->Escape($personName).'", ' // should be name, not id here                                                          
                        . $institutionID . ', '                                                                                                      
                        . '"'.$db->Escape($institutionName).'", '                                                                                    
                        . $k . ', ' // week id                                                                                                       
                        . '"'. $db->Escape($weeks[$k]['dates']) . '", '                                                                              
                        . '"'. $db->Escape($weeks[$k]['times']) . '", '                                                                              
                        . '"'. $db->Escape($weeks[$k]['shiftType']) . '", '
			. $shift_hours . ', '
			. $shift_type                                                                            
                        . ')';                                                                                                                       
                    $db->Query($sql);                                 
		}   

		if ($shift_type == 6) {
		    // insert shift leader training
		    $db->Query('INSERT INTO `ShiftSignup`.ShiftTraining (shiftTypeID, personID, beginTime, isPending) VALUES (2, '.
			$person.', FROM_UNIXTIME('.$shift_weeks[$k]['start'].'), 0)');
		}
		if ($shift_type == 7) {
		    // insert det op training
		    $db->Query('INSERT INTO `ShiftSignup`.ShiftTraining (shiftTypeID, personID, beginTime, isPending) VALUES (3, '.
			$person.', FROM_UNIXTIME('.$shift_weeks[$k]['start'].'), 0)');
		}
		//$tpl->set('message', '<font color="green">request successful</font>');
	    } else {
		$erm = $db->GetErrorMessage();
		if (strstr($erm, 'cannot be null') !== false) { 
		  // $weeks[$k]['operation'] = '&nbsp;<font color="red">Failed - oversubscription limit reached&nbsp;</font>';
		  $weeks[$k]['operation'] = '&nbsp;<font color="red">Failed - institution has reached its quota&nbsp;</font>';
		} else if (strstr($erm, 'uplicate entry') !== false) {
		    $weeks[$k]['operation'] = '&nbsp;<font color="red">Failed - slot already taken by somebody else&nbsp;</font>';
		} else {
		    $weeks[$k]['operation'] = '&nbsp;<font color="red">Failed. Please contact your database admin : '.$erm.'</font>';
		}
	    }
	} else if (intval($v) < 0) {

	    $run_start = $cfg->Get('run', 'run_start_date');
	    $run_start_seconds = strtotime($run_start);
	    $nweeks = intval($cfg->Get('run', 'number_of_weeks_total'));
	    $cur_time = time();
	    $current_week = -1;
	    $inst_threshold = intval($cfg->Get('generic', 'institution_unassign_threshold'));
	    for ($i = 0; $i < $nweeks; $i++) {
    		$tm_st = $run_start_seconds + $i * 7 * 24 * 3600;
    		if ($tm_st < $cur_time) { $current_week = $i; }
	    }
	    if (($shift_type <= 5) && (($k - $current_week) < $inst_threshold) ) {
			note_to_maillist($person, $shift_type, $shift_hours, $k);
	    }

	    // delete old entry
	    $sql = 'DELETE FROM `ShiftSignup`.Shifts WHERE personID = '.$person;
	    $sql .= ' AND week = '.$k.' AND shiftNumber = '.$shift_hours.' AND shiftTypeID = '.$shift_type;//  AND institution_id = '.$institutionID;
	    $db->Query($sql);
	    $weeks[$k]['operation'] = '<font color="orange">Removed</font>';

		$tmp = $cfg->Get('access', 'log_user_actions');
		if (!empty($tmp)) {
                    $origin_ip    = get_ip_address();                                                                                         
                    $origin_host  = $_SERVER['REMOTE_HOST'];                                                                                         
                    if (empty($origin_host)) {                                                                                                       
                        $origin_host = gethostbyaddr($origin_ip);                                                                                    
                    }                                                                                                                                
                    $origin_agent = $_SERVER['HTTP_USER_AGENT'];                                                                                     
                                                                                                                                                     
                    $sql = 'INSERT INTO `ShiftSignup`.`ShiftActions` (action_type, origin_ip, origin_host, origin_agent, '                           
                        . 'user_id, user_name, inst_id, inst_name, '                                                                                 
                        . 'week_id, week_name, shift_slot, shift_type, shift_slot_id, shift_type_id) VALUES ("removed", '                                                          
                        . '"'.$db->Escape($origin_ip).'", '                                                                                          
                        . '"'.$db->Escape($origin_host).'", '                                                                                        
                        . '"'.$db->Escape($origin_agent).'", '                                                                                       
                        . $person . ', '                                                                                                             
                        . '"'.$db->Escape($personName).'", ' // should be name, not id here                                                          
                        . $institutionID . ', '                                                                                                      
                        . '"'.$db->Escape($institutionName).'", '                                                                                    
                        . $k . ', ' // week id                                                                                                       
                        . '"'. $db->Escape($weeks[$k]['dates']) . '", '                                                                              
                        . '"'. $db->Escape($weeks[$k]['times']) . '", '                                                                              
                        . '"'. $db->Escape($weeks[$k]['shiftType']) . '", '
			. $shift_hours . ', '
			. $shift_type                                       
                        . ')';                                                                                                                       
                    $db->Query($sql);                                    
		}

	    if ($shift_type == 6) {
		// check if we need to remove shift leader training
		$db->Query('DELETE FROM `ShiftSignup`.ShiftTraining WHERE personID = '.
		   $person.' AND shiftTypeID = 2 AND beginTime = FROM_UNIXTIME('.$shift_weeks[$k]['start'].')');
	    }
	    if ($shift_type == 7) {
		// check if we need to remove det op training
		$db->Query('DELETE FROM `ShiftSignup`.ShiftTraining WHERE personID = '.
		   $person.' AND shiftTypeID = 3 AND beginTime = FROM_UNIXTIME('.$shift_weeks[$k]['start'].')');
	    }
	} else {
	    // something strange happened..	    
	    echo 'ERROR? Shift value: '.$v;
	    $weeks[$k]['operation'] = '<font color="red">Error: </font>';
	}
    }
    } else {
	$tpl->set('message', '<font color="red">operation cancelled by user</font>');
    }

    $tpl->set('weeks', $weeks);
    $tpl->set('personID', $person);
    $tpl->set('personName', $personName);
    $tpl->set('institutionId', $institutionID);
    $tpl->set('institutionName', $institutionName);

    $tpl->set_file('finalizesignup.tpl.php');
}