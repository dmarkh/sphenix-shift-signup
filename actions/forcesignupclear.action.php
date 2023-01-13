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

function getShiftType($id) {
    switch($id) {
	case 1:
	    return 1;
	    break;
	case 2:
	case 6:
	    return 2;
	    break;
	case 3:
	case 4:
	case 7:
	    return 3;
	    break;
	default:
	    return -1;
	    break;	
    }
}

function forcesignupclear_action() {

    $cfg =& Config::Instance(); 
    $db  =& Db::Instance();
    $week = intval($_GET['week']);
    $shiftNumber = intval($_GET['shiftNumber']);
    $shiftTypeID = intval($_GET['shiftTypeID']);
    $trnCheck = intval($_GET['trnCheck']);                                                                                                                              
                                                                                                                                                                        
    $tm = strtotime($cfg->Get('run', 'run_start_date'));                                                                                                                
    $tm_st = $tm + $week * 7 * 24 * 3600;                                                                                                                               
    $tm_en = $tm + ($week+1) * 7 * 24 * 3600;

//********************* CHECK IF SOME PERSON IS HOLDING THIS SLOT *********
    if ($trnCheck == 1 && in_array( $shiftTypeID, array(1,2,3,4,6,7) ) ) {
	$sql = 'SELECT personID FROM `ShiftSignup`.`Shifts` WHERE week = '.$week.' AND shiftNumber = '.$shiftNumber.' AND shiftTypeID = '.$shiftTypeID.' LIMIT 1';
	$res = $db->Query($sql);
	if ( !empty($res) ) {
	    // check training and move/delete when neccesary
	    $personID = intval($res['personID'][0]);
	    $sql = 'SELECT UNIX_TIMESTAMP(beginTime) as bt FROM `ShiftSignup`.`ShiftTraining` WHERE personID = '.$personID.' AND shiftTypeID = '.getShiftType($shiftTypeID);
	    $res = $db->Query($sql);
	    if (!empty($res)) {
		$bt = $res['bt'][0];
		if ($bt == $tm_st) {
		    $type = getShiftType($shiftTypeID);
		    $types = '';
		    switch($type) {
			case 1:
			    $types = '1';
			    break;
			case 2:
			    $types = '2,6';
			    break;
			case 3:
			    $types = '3,4,7';
			    break;
			default:
			    $types = '';
			    break;
		    }
		    // check taken slots (same type) later than this one
		    $sql = 'SELECT week FROM `ShiftSignup`.`Shifts` WHERE personID = '.$personID
			.' AND ShiftTypeID IN('.$types.') AND week > '.$week.' ORDER BY beginTime ASC LIMIT 1';
		    $res = $db->Query($sql);
		    if (!empty($res)) {
			// found slot of same type
			$mweek = $res['week'][0];
			$mbt = $tm + $mweek * 7 * 24 * 3600;
			// update training entry
			$sql = 'UPDATE `ShiftSignup`.`ShiftTraining` SET beginTime = FROM_UNIXTIME('.$mbt.') WHERE personID = '.$personID.' AND shiftTypeID = '.$type;
			$db->Query($sql);
		    } else {
			$sql = 'DELETE FROM `ShiftSignup`.`ShiftTraining` WHERE personID = '.$personID.' AND shiftTypeID = '.$type;
			$db->Query($sql);
		    }
		}
	    }
	}
    } 

//*************************** CLEAR SLOT **********************************
    $sql = 'DELETE FROM `ShiftSignup`.`Shifts` WHERE week = '.$week.' AND shiftNumber = '.$shiftNumber.' AND shiftTypeID = '.$shiftTypeID.' LIMIT 1';
    $res = $db->Query($sql);

    header("Location: ?do=forcesignup");
}