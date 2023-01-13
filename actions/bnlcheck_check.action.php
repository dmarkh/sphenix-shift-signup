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

function bnlcheck_compare_names($str1, $str2) {                                                                                                                                  
    $common = min(strlen($str1), strlen($str2));                                                                                                                        
    if (levenshtein( strtolower(substr($str1, 0, $common)), strtolower(substr($str2, 0, $common)) ) <= 2) { return true; }                                              
    if (levenshtein( strtolower(substr(strrev($str1), 0, $common)), strtolower(substr(strrev($str2), 0, $common)) ) <= 2) { return true; }                              
    return false;                                                                                                                                                       
} 

function bnlcheck_check_action() {
    $cfg =& Config::Instance();
    $db  =& Db::Instance();
    if ( $cfg->Get('run', 'enable_bnl_id_check') == true && ( !isset($_COOKIE['labid']) || !empty($_GET['ignore']) ) ) {
	$inst = $_GET['inst'];
	$pers = $_GET['pers'];
	$uid = $_GET['labid'];
	if (empty($inst) || empty($pers) || empty($uid)) {
	    return json_encode(array('error' => 'missing params'));
	}

	$members = get_shifttable_members();
	/*
	// get user name from phonebook:
	$query = 'SELECT * FROM `starweb`.`members` WHERE Id = '.intval($pers).' LIMIT 1';
	$res = $db->Query($query);
	if (empty($res) || empty($res[0]['FirstName']) || empty($res[0]['LastName'])) { echo json_encode(array('error' => 'cannot find user in db')); exit; }
	$fname = strtolower($res[0]['FirstName']);
	$lname = strtolower($res[0]['LastName']);
	*/
	if ( empty($members[intval($pers)]) ) { echo json_encode(array('error' => 'cannot find user in db')); exit; }
	$fname = strtolower($members[intval($pers)]['FirstName']);
	$lname = strtolower($members[intval($pers)]['LastName']);

	$trq = TrainReq::Instance();                                                                                                                                            
	$trq->set_id_phonebook(intval($pers));
	$trq->set_lname($lname);
	$check_result = $trq->check_user($uid);

	$itd_fname = strtolower($check_result['itd']['name_first']);
	$itd_lname = strtolower($check_result['itd']['name_last']);

	// compare names using levenshtein, also consider long spanish names vs short forms
	$bnl_check_result = bnlcheck_compare_names($lname, $itd_lname);
	if ($bnl_check_result !== true ) {
	    echo json_encode(array('error' => 'names do not match, '.$fname.' '.$lname.' != '.$itd_fname.' '.$itd_lname)); exit;
	}

	// compare names using levenshtein
	//$fname_lev = levenshtein($fname, $itd_fname);
	//$lname_lev = levenshtein($lname, $itd_lname);
	//if ($lname_lev > 3) {
	//    echo json_encode(array('error' => 'names do not match, '.$fname.' '.$lname.' != '.$itd_fname.' '.$itd_lname)); exit;
	//}
	// names match
	echo json_encode($check_result); exit;
    }
}
