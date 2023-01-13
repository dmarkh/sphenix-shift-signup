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

function missingid_action() {
    $cfg =& Config::Instance();
    $tpl =& Template::Instance();
    $db  =& Db::Instance();

    // 1. select persons on shift for the next two weeks:                                                                                                                    
    $query = 'SELECT personID, shiftTypeID, beginTime, endTime FROM `ShiftSignup`.`Shifts` WHERE `beginTime` BETWEEN DATE_SUB(NOW(), INTERVAL 1 WEEK) AND DATE_ADD(NOW(), INTERVAL 2 WEEK) ORDER BY beginTime ASC';
    $persons = $db->Query($query);
    if ( empty($persons) || !is_array($persons) ) { echo 'NO PERSONS ON SHIFT? Has Run ended or not started yet?'; exit; } // no persons on shift.. is Run over already?

    $leader_ids = array();
    $shifter_ids = array();
    $missingid_ids = array();
    $check_results = array();

    // 2. get unique person list, update ITD requirements info for them:                                                                                                     
    $members = get_shifttable_members();

    foreach ($persons as $k => $v) {                                                                                                                                        
	$query = 'SELECT * FROM `ShiftSignup`.`ShiftReqs` WHERE id_phonebook = '.intval($v['personID']).' LIMIT 1';                                                         
	$res = $db->Query($query);                                                                                                                                          
	if (!empty($res) && isset($res[0])) {                                                                                                                               
	    /*
    	    $res = $res[0];                                                                                                                                                 
    	    // check user with $v['personID'] as phonebook_id, $res['id_bnl'] as bnl id                                                                                     
    	    $trq = TrainReq::Instance();                                                                                                                                    
    	    $trq->set_id_phonebook(intval($v['personID']));                                                                                                                 
    	    $trq->set_lname($res['itd_name_last']);                                                                                                                         
    	    $check_result = $trq->check_user($res['id_bnl']); // ignore result..                                                                                            
    	    $check_results[$v['personID']] = $check_result;                                                                                                                 
	    */
	} else {
	    if ( !empty($members[$v['personID']]) ) {
		$missingid_ids[] = $members[$v['personID']];
	    }
	    /*
	    $query = 'SELECT a.*, b.InstitutionName, b.Id as inst_id FROM `starweb`.`members` a, `starweb`.`institutions` b WHERE a.Id = '.intval($v['personID']).' AND a.InstitutionId = b.Id';
	    $res = $db->Query($query);
	    if (!empty($res) && isset($res[0])) {
		$missingid_ids[] = $res[0];
	    }
	    */
	}
    }

    $tpl->set('noid', $missingid_ids);                                                                                                                                          
    $tpl->set_file('missingid.tpl.php');
}
