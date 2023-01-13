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

function updateexperts_action() {

    $tpl =& Template::Instance();    
    $cfg =& Config::Instance();
    $db  =& Db::Instance();

    $experts = get_expert_members();

    //print_r($_POST);
    if (!empty($_POST) && !empty($_POST['exOp'])) {
	if (isset($_POST['submit'])) {
	$phdb = false;
	if (strtolower($_POST['submit']) == 'update') {
	    $phdb = new Db();
	    $phdb->InitFromConfig('phonebook_database');
	}

	if (is_array($_POST['exOp'])) {
	foreach($_POST['exOp'] as $k => $v) {
	    if (strtolower($v) == 'delete') {
		// delete this expert from local table
		$db->Query('DELETE FROM `ShiftSignup`.`ShiftExperts` WHERE id = '.intval($k));

		$cat = $db->Query('SELECT * FROM `ShiftSignup`.`ShiftExpertiseCategories` WHERE 1');
                foreach($cat as $k2 => $v2) {
                    if (empty($v2['catExperts'])) continue;
                    $ex = explode(',', $v2['catExperts']);
                    $rm = array(); $rm[] = intval($k);
                    if (in_array(intval($k), $ex)) {
                        // found expert, remove this guy and update category
                        $ids = array_diff($ex, $rm);
                        $ids = implode(',', $ids);
                        $sql = 'UPDATE `ShiftSignup`.`ShiftExpertiseCategories` SET catExperts = "'.$ids.'" WHERE id = '.$v2['id'];
                        $db->Query($sql);
                    }
                }

	    } else if (strtolower($v) == 'disable') {
		// set "disable" bit
		$db->Query('UPDATE `ShiftSignup`.`ShiftExperts` SET exDisabled = 1 WHERE id = '.intval($k));
	    } else if (strtolower($v) == 'restore') {
		$db->Query('UPDATE `ShiftSignup`.`ShiftExperts` SET exDisabled = 0 WHERE id = '.intval($k));
	    } else {
		// update fields...
		$phPrim = $_POST['exPhonePrimary'][$k];
		$phone = $_POST['exPhone'][$k];
		$phCell = $_POST['exPhoneCell'][$k];
		$phBnl = $_POST['exPhoneBnl'][$k];
		$phHome = $_POST['exPhoneHome'][$k];
		$email = mysql_real_escape_string($_POST['exEmail'][$k]);
		$exID = intval($_POST['exID'][$k]);
		$exFirstName = $_POST['exFirstName'][$k];
		$exLastName = $_POST['exLastName'][$k];
		$exCredit = $_POST['ecred'][$k];
		$exDescription = mysql_real_escape_string($_POST['exDescription'][$k]);
		$sql = 'UPDATE `ShiftSignup`.`ShiftExperts` SET exDescription = "'.$exDescription.'", exPhonePrimary = "'.$phPrim.'", exPhone = "'.$phone.'", exPhoneCell = "'.$phCell.
		'", exPhoneBnl = "'.$phBnl.'", exPhoneHome = "'.$phHome.'", exEmail = "'.$email.'" WHERE id = '.intval($k).' AND exDisabled = 0';
		$db->Query($sql);
		// update primary phonebook:
		/*
		if ($phdb) {
		    $sql = 'UPDATE `starweb`.`members` SET Phone = "'.$phone.'", CellPhone = "'.$phCell.'", BnlPhone = "'.$phBnl.
		    '", EmailAddress = "'.$email.'", ExpertCredit='.$exCredit.'  WHERE Id = '.$exID.' AND FirstName = "'.$exFirstName.'" AND LastName = "'.$exLastName.'"';
		    $phdb->Query($sql);
		    $db->Query($sql);
		}
		*/
	    }
	}
	}
	if (strtolower($v) == 'update' && $phdb) {
	    $phdb->Close();
	}

	$tpl->set('message', 'Local Expert List Updated');
	} else if (isset($_POST['sync'])) {

	    $m = $db->Query('SELECT * FROM `ShiftSignup`.ShiftExperts WHERE 1 ORDER BY exLastName ASC');
	    //$m = $db->Query('SELECT a.*, b.ExpertCredit as ecred FROM `ShiftSignup`.`ShiftExperts` a, `starweb`.`members` b WHERE a.exID = b.ID ORDER BY a.exLastName ASC');
	    $exp = array();
	    foreach($m as $k => $v) {
		$exp[$v['exFirstName'].$v['exLastName']] = 1;
	    }
	    //$ph = $db->Query('SELECT m.*, i.InstitutionName, i.Country FROM `starweb`.members m, `starweb`.institutions i WHERE m.isExpert = "Y" AND m.InstitutionId = i.Id ORDER BY LastName ASC');

	    $ph = $experts;
	    $message = '';

	    // check if we have different lists for local and original experts:
	    $exp_in = array();

	    foreach($ph as $k => $v) {
		$exists = false;
		foreach($m as $k2 => $v2) {
		    if ($v['Id'] == $v2['exID']) {
			$exists = true;
			$m[$k2]['stays'] = true;
			break;
		    }
		}
		if (!$exists) {
		    $exp_in[] = $v;
		}
	    }

	    foreach($exp_in as $k => $v) {
		if (!isset($exp[$v['FirstName'].$v['LastName']])) {
		    $sql = 'INSERT INTO `ShiftSignup`.ShiftExperts (exID, exFirstName, exLastName, exInstitutionID, exInstitutionName, '.
    			'exPhonePrimary, exPhone, exPhoneCell, exPhoneBnl, exEmail, exComment) '.
    			'VALUES ('.
    			$v['Id'].', "'.$v['FirstName'].'","'.$v['LastName'].'", '.$v['InstitutionId'].', "'.$v['InstitutionName'].'", "'.
			$v['Phone'].'", "'.
    			$v['Phone'].'", "'.$v['CellPhone'].'", "'.$v['BnlPhone'].'", "'.$v['EmailAddress'].'", "'.$v['Expertise'].'")';
    		    $db->Query($sql);
		    $message .= 'Added expert to local list: '.$v['FirstName'].' '.$v['LastName'].'<br>';
		}
	    }

	    foreach($m as $k => $v) {
		if (!empty($v['stays'])) continue;
		$sql = 'DELETE FROM `ShiftSignup`.`ShiftExperts` WHERE exID = '.$v['exID'].' AND exFirstName = "'.$v['exFirstName'].
			'" AND exLastName = "'.$v['exLastName'].'" LIMIT 1';
		$db->Query($sql);
		// remove expert from categories too
		$cat = $db->Query('SELECT * FROM `ShiftSignup`.`ShiftExpertiseCategories` WHERE 1');
		foreach($cat as $k2 => $v2) {
		    if (empty($v2['catExperts'])) continue;
		    $ex = explode(',', $v2['catExperts']);
		    $rm = array(); $rm[] = $v['id'];
		    if (in_array($v['id'], $ex)) {
			// found expert, remove this guy and update category
			$ids = array_diff($ex, $rm);
                	$ids = implode(',', $ids);	
			$sql = 'UPDATE `ShiftSignup`.`ShiftExpertiseCategories` SET catExperts = "'.$ids.'" WHERE id = '.$v2['id'];
                	$db->Query($sql);
		    }
		}		

		$message .= 'Removed expert from local list: '.$v['exFirstName'].' '.$v['exLastName'].' - not an expert anymore<br>';
	    }

	    if (!empty($message)) {
		$tpl->set('message', 'CHANGES: <BR>'.$message);
	    } else {
		$tpl->set('message', 'NO NEW EXPERTS FOUND IN PHONEBOOK, NOTHING TO IMPORT');
	    }
    	} else {
    
	}
    }

    $cat = $db->Query('SELECT * FROM `ShiftSignup`.ShiftExpertiseCategories WHERE 1 ORDER BY catWeight ASC');    
    $refs = array();
    $list = array();
    if (!empty($cat) && is_array($cat)) {
        foreach($cat as $k => $v) {
            $thisref = &$refs[ $v['id'] ];
            $thisref['id'] = $v['id'];
            $thisref['w'] = $v['catWeight'];
            $thisref['parentId'] = $v['parentId'];
            $thisref['name'] = $v['catName'];
            if ($v['parentId'] == 0) {
                $list[ $v['id'] ] = &$thisref;
            } else {
                $refs[ $v['parentId'] ]['children'][ $v['id'] ] = &$thisref;
            }
        }
    }
    
    $m = $db->Query('SELECT * FROM `ShiftSignup`.ShiftExperts WHERE 1 ORDER BY exLastName ASC');    
    //$m = $db->Query('SELECT a.*, b.ExpertCredit as ecred FROM `ShiftSignup`.`ShiftExperts` a, `starweb`.`members` b WHERE a.exID = b.ID ORDER BY a.exLastName ASC');
    foreach($m as $k => $v) {
	if ( !empty($experts[ $v['exID'] ]['ExpertCredit']) ) { 
	    $m[$k]['ExpertCredit'] = intval($experts[ $v['exID'] ]['ExpertCredit']); 
	    $m[$k]['ecred'] = intval($experts[ $v['exID'] ]['ExpertCredit']); 
        } else {                                                                                                                                                        
            $m[$k]['ExpertCredit'] = 0;                                                                                                                                 
            $m[$k]['ecred'] = 0;                                                                                                                                        
        }
    }

    $tpl->set('cat', $list);
    $tpl->set('experts', $m);
    $tpl->set_file('manageexperts.tpl.php');
}