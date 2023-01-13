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

function importexperts_action() {

    $tpl =& Template::Instance();    
    $cfg =& Config::Instance();
    $db  =& Db::Instance();

    $exp = $db->Query('SELECT m.*, i.InstitutionName, i.Country FROM `starweb`.members m, `starweb`.institutions i WHERE m.isExpert = "Y" AND m.InstitutionId = i.Id ORDER BY LastName ASC');
    foreach($exp as $k => $v) {
	$sql = 'INSERT INTO `ShiftSignup`.ShiftExperts (exID, exFirstName, exLastName, exInstitutionID, exInstitutionName, exCategoryID,'.
	'exPhonePrimary, exPhone, exPhoneCell, exPhoneBnl, exEmail, exComment) '.
	'VALUES ('.
	$v['Id'].', "'.$v['FirstName'].'","'.$v['LastName'].'", '.$v['InstitutionId'].', "'.$v['InstitutionName'].'", 0, "'.
	$v['BnlPhone'].'", "'.$v['Phone'].'", "'.
	$v['CellPhone'].'", "'.$v['BnlPhone'].'", "'.$v['EmailAddress'].'", "'.$v['Expertise'].'")';
	$db->Query($sql);
    }    
    
    $mem = $db->Query('SELECT * FROM `ShiftSignup`.ShiftExperts WHERE 1');    
    
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
    if (is_array($mem) && !empty($mem)) {
	$tpl->set('message', 'IMPORTED '.count($mem).' EXPERTS FROM PHONEBOOK');
    }

    $tpl->set('experts', $mem);
    $tpl->set('cat', $list);
    $tpl->set_file('manageexperts.tpl.php');
}