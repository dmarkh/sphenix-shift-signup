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

function submitsignup_action() {

    $person = intval($_POST['personID']);
    $personName = $_POST['personName'];
    $institutionID = intval($_POST['institutionID']);
    $institutionName = $_POST['institutionName'];

    $signupType = intval($_POST['signupType']); // 1 = generic, 2 = offline qa, 3 = spin?

    $tpl =& Template::Instance();    
    $cfg =& Config::Instance();
    $db  =& Db::Instance();

    $wk = $_POST['week']; // -123, or 345

    $weeks = array();

    $tm = strtotime($cfg->Get('run', 'run_start_date'));

    syslog( LOG_WARNING, 'run_start_date: '.$cfg->Get('run', 'run_start_date').' = '.$tm );

    $shift_leader_training = false;
    $detector_operator_training = false;
    foreach($wk as $k => $v) {
	$shift_type = abs(intval($v/100));
	if ($shift_type == 6 && $shift_leader_training == true) { continue; } // skip repeated trainings
	if ($shift_type == 7 && $detector_operator_training == true) { continue; } // skip repeated trainings
	if ($shift_type == 6) { $shift_leader_training = true; }
	if ($shift_type == 7) { $detector_operator_training = true; }

	$weeks[$k]['value'] = $v;
 	$tm_st = $tm + $k * 7 * 24 * 3600;
	$tm_en = $tm + ($k+1) * 7 * 24 * 3600;
	$weeks[$k]['dates'] = date('M j', $tm_st).'<small>'.date('S', $tm_st).'</small> - '.date('M j', $tm_en).'<small>'.date('S',$tm_en).'</small>';;
	$weeks[$k]['times'] = get_shift_hours(abs(intval($v % 10)));
	$weeks[$k]['shiftType'] = get_shift_type($shift_type);
	if (intval($v) > 0) { 
	    $weeks[$k]['operation'] = '<font color="green">Add</font>';
	} else {
	    $weeks[$k]['operation'] = '<font color="red">Remove</font>';
	}
    }
    
    $tpl->set('weeks', $weeks);

    $tpl->set('personID', $person);
    $tpl->set('personName', $personName);
    $tpl->set('institutionID', $institutionID);
    $tpl->set('institutionName', $institutionName);

    $tpl->set_file('submitsignup.tpl.php');
}