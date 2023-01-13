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

function forcesignupinsert_action() {

    $cfg =& Config::Instance(); 
    $db  =& Db::Instance();
    $week = intval($_GET['week']);
    $shiftNumber = intval($_GET['shiftNumber']);
    $shiftTypeID = intval($_GET['shiftTypeID']);
    $personID = intval($_GET['personID']);    
    $replaceInst = intval($_GET['replaceInst']);
    $trnCheck = intval($_GET['trnCheck']);

    $tm = strtotime($cfg->Get('run', 'run_start_date'));
    $tm_st = $tm + $week * 7 * 24 * 3600;                                                                                                                              
    $tm_en = $tm + ($week+1) * 7 * 24 * 3600; 


//*************************** CHECK if slot is empty or not *******************************************

    $sql = 'SELECT personID from `ShiftSignup`.`Shifts` WHERE week = '.$week.' AND shiftNumber = '.$shiftNumber.' AND shiftTypeID = '.$shiftTypeID.' LIMIT 1';
    $res = $db->Query($sql);
    $slot_is_free = true;
    if (empty($res)) {
	// FREE SLOT
    } else {
	// SLOT IS *NOT* FREE (taken by someone)
	$slot_is_free = false;
	$res = $res['personID'][0];
    }

//*************************** CHECK TRAINING TYPE for a person ****************************************
    if ($trnCheck == 1) {
	// Period Coordinator training type : 1
	// Shift Leader       training type : 2
	// Detector Operator  training type : 3
	$sql = 'SELECT shiftTypeID, personID, UNIX_TIMESTAMP(beginTime) as bt FROM `ShiftSignup`.`ShiftTraining` WHERE personID = '.$personID;
	$trn = $db->Query($sql);
	$has_per_trn = false;
	$has_shl_trn = false;
	$has_dop_trn = false;
	$per_trn_ts = false;
	$shl_trn_ts = false;
	$dop_trn_ts = false;
	foreach($trn as $k => $v) {
	    if ($v['shiftTypeID'] == 1) { $has_per_trn = true; $per_trn_ts = $v['bt']; }
	    if ($v['shiftTypeID'] == 2) { $has_shl_trn = true; $shl_trn_ts = $v['bt']; }
	    if ($v['shiftTypeID'] == 3) { $has_dop_trn = true; $dop_trn_ts = $v['bt']; }
	}
    }
//************************* INSERT TRAINING IF SLOT IS FREE AND PERSON HAS NO TRAINING OF SUCH TYPE *********

if ( $trnCheck == 1 ) {
    $sql = '';
    if ($shiftTypeID == 1 && !$has_per_trn) {
	// insert period coordinator training
	$sql = 'INSERT INTO `ShiftSignup`.`ShiftTraining` (shiftTypeID, personID, beginTime, isPending) VALUES (1, '.$personID.', FROM_UNIXTIME('.$tm_st.'), 0 )';
	$db->Query($sql);
    } else if ($shiftTypeID == 1 && $has_per_trn) {
	// move period coordinator training timestamp if later than this week
	if ($tm_st < $per_trn_ts) {
	    $sql = 'UPDATE `ShiftSignup`.`ShiftTraining` SET beginTime = FROM_UNIXTIME('.$tm_st.') WHERE personID = '.$personID.' AND shiftTypeID = 1';
	    $db->Query($sql);
	}
    } else if ( in_array( $shiftTypeID, array(2,6) ) && !$has_shl_trn) {
	// insert shift leader training, if missing
	$sql = 'INSERT INTO `ShiftSignup`.`ShiftTraining` (shiftTypeID, personID, beginTime, isPending) VALUES (2, '.$personID.', FROM_UNIXTIME('.$tm_st.'), 0 )';	
	$db->Query($sql);
    } else if (in_array($shiftTypeID, array(2,6)) && $has_shl_trn) {
	// move shift leader training timestamp if later than this week
	if ($tm_st < $shl_trn_ts) {
	    $sql = 'UPDATE `ShiftSignup`.`ShiftTraining` SET beginTime = FROM_UNIXTIME('.$tm_st.') WHERE personID = '.$personID.' AND shiftTypeID = 2';
	    $db->Query($sql);
	}
    } else if ( in_array( $shiftTypeID, array(3,4,7) ) && !$has_dop_trn) {
	// insert det.op. training
	$sql = 'INSERT INTO `ShiftSignup`.`ShiftTraining` (shiftTypeID, personID, beginTime, isPending) VALUES (3, '.$personID.', FROM_UNIXTIME('.$tm_st.'), 0 )';
	$db->Query($sql);
    } else if (in_array($shiftTypeID, array(3,4,7)) && $has_dop_trn) {
	// move shift leader training timestamp if later than this week
	if ($tm_st < $dop_trn_ts) {
	    $sql = 'UPDATE `ShiftSignup`.`ShiftTraining` SET beginTime = FROM_UNIXTIME('.$tm_st.') WHERE personID = '.$personID.' AND shiftTypeID = 3';
	    $db->Query($sql);
	}
    }
}


//************************* INSERT OR REPLACE SHIFT SLOT ****************************************************
/*
    $sql = 'SELECT m.InstitutionId, i.InstitutionName FROM `starweb`.members m, `starweb`.institutions i WHERE m.Id = '.$personID.' AND m.InstitutionId = i.Id';
    $inst = $db->Query($sql);
    if (is_array($inst[0])) { $inst = $inst[0]; }
*/
    $inst = get_inst_id_name_from_member($personID);

    if ($replaceInst == 1) {
	$sql = 'REPLACE INTO `ShiftSignup`.`Shifts` (beginTime, endTime, week, shiftNumber, shiftTypeID, personID, institution_id, institution, bypass) VALUES';
	$sql .= '(FROM_UNIXTIME('.$tm_st.'), FROM_UNIXTIME('.$tm_en.'), '.$week.','.$shiftNumber.','.$shiftTypeID.','.$personID.','.$inst['InstitutionId'].',"'.$inst['InstitutionName'].'",1)';
	$db->Query($sql);
    } else {
	// have to keep institution ID dependence..
	$sql = 'SELECT s.personID, s.institution_id, s.institution FROM `ShiftSignup`.`Shifts` s WHERE s.week = '.$week.' AND s.shiftNumber = '.$shiftNumber.' AND s.shiftTypeID = '.$shiftTypeID;
	$old = $db->Query($sql);
	$old = $old[0];
	if ($inst['InstitutionId'] != $old['institution_id'] && substr($old['institution'], 0, 12) != "<b>PROXY FOR" ) { $old['institution'] = '<b>PROXY FOR</b>:'.$old['institution']; }
	$sql = 'REPLACE INTO `ShiftSignup`.`Shifts` (beginTime, endTime, week, shiftNumber, shiftTypeID, personID, institution_id, institution, bypass) VALUES';
	$sql .= ' (FROM_UNIXTIME('.$tm_st.'), FROM_UNIXTIME('.$tm_en.'), '.$week.','.$shiftNumber.','.$shiftTypeID.','.$personID.','.$old['institution_id'].',"'.$old['institution'].'",1)';
	$db->Query($sql);
	
    }

    header("Location: ?do=forcesignup");
}