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

function addexpert_action() {

    $tpl =& Template::Instance();    
    $cfg =& Config::Instance();
    $db  =& Db::Instance();

    $catID = intval($_POST['catID']);

    $c = $db->Query('SELECT * FROM `ShiftSignup`.`ShiftExpertiseCategories` WHERE id = '.$catID);

    if (!empty($_POST['expertID'])) {
	$id = intval($_POST['expertID']);
	
	if ($_POST['do'] == 'addexpert') {
	    if (empty($c[0]['catExperts'])) {
		$db->Query('UPDATE `ShiftSignup`.`ShiftExpertiseCategories` SET catExperts = "'.$id.'" WHERE id = '.$catID);
	    } else {
		$ids = explode(',', $c[0]['catExperts']);
		if (!in_array($id, $ids)) {
		    $ids[] = $id;
		    $ids = implode(',', $ids);
		    $db->Query('UPDATE `ShiftSignup`.`ShiftExpertiseCategories` SET catExperts = "'.$ids.'" WHERE id = '.$catID);
		}
	    }
	    $tpl->set('message', 'expert entry added to this category');
	} else if ($_POST['do'] == 'removeexpert') {
	    $ids = explode(',', $c[0]['catExperts']);
	    $ids = array_diff($ids, array($id));
	    $ids = implode(',', $ids);
	    $db->Query('UPDATE `ShiftSignup`.`ShiftExpertiseCategories` SET catExperts = "'.$ids.'" WHERE id = '.$catID);
	    $tpl->set('message', 'expert entry removed from this category');
	} else {
	    echo 'HMM?';
	}
    }

    $c = $db->Query('SELECT * FROM `ShiftSignup`.`ShiftExpertiseCategories` WHERE id = '.$catID);
    $m = $db->Query('SELECT * FROM `ShiftSignup`.`ShiftExperts` WHERE 1 ORDER BY exLastName');
    if (!empty($c) && !empty($m)) {
	$mem = array();
	foreach($m as $k => $v) {
    	    $mem[$v['id']] = $v;
	}
	$tpl->set('cat', $c[0]);
	$tpl->set('experts', $mem);
    }
    $tpl->set_file('editexpertlist.tpl.php');

}