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

function modifycategory_action() {

    $tpl =& Template::Instance();    
    $cfg =& Config::Instance();
    $db  =& Db::Instance();

    if (!empty($_POST) && is_array($_POST) && !empty($_POST['catAction'])) {
	switch($_POST['catAction']) {
	    case 'rename':
		$id = intval($_POST['catID']);
		$newname = $_POST['catName'];
		$sql = 'UPDATE `ShiftSignup`.ShiftExpertiseCategories SET catName = \''.$newname.'\' WHERE id = '.$id;
		$r = $db->Query($sql); 
		break;
	    case 'delete':
		$id = intval($_POST['catID']);
		$db->Query('DELETE FROM `ShiftSignup`.ShiftExpertiseCategories WHERE id = '.$id); 
		$db->Query('UPDATE `ShiftSignup`.`ShiftExperts` SET exCategoryID = 0 WHERE exCategoryID = '.$id);
		break;
	    case 'insert':
		$id = intval($_POST['catID']);
		$name = $_POST['catName'];
		$sql = 'INSERT INTO `ShiftSignup`.ShiftExpertiseCategories (parentId, catName) VALUES ('.$id.', \''.$name.'\')';
		$r = $db->Query($sql); 
		break;
	    case 'moveup':
		$id = intval($_POST['catID']);
		$sql = 'UPDATE `ShiftSignup`.ShiftExpertiseCategories SET catWeight = (catWeight - 1) WHERE id = '.$id;
		$r = $db->Query($sql); 
		break;
	    case 'movedown':
		$id = intval($_POST['catID']);
		$sql = 'UPDATE `ShiftSignup`.ShiftExpertiseCategories SET catWeight = (catWeight + 1) WHERE id = '.$id;
		$r = $db->Query($sql); 
		break;
            case 'reorder':
                $ids_txt = trim($_POST['catID'], ' ,');
                $ids = explode(',', $ids_txt);
                foreach($ids as $k => $v) {
                    $sql = 'UPDATE `ShiftSignup`.ShiftExpertiseCategories SET catWeight = '.intval($k).' WHERE id = '.intval($v).' LIMIT 1';
                    $r = $db->Query($sql);
                }
                break;
	    case 'editexperts':
		$c = $db->Query('SELECT * FROM `ShiftSignup`.`ShiftExpertiseCategories` WHERE id = '.intval($_POST['catID']));
		$m = $db->Query('SELECT * FROM `ShiftSignup`.`ShiftExperts` WHERE 1 ORDER BY exLastName');
		$mem = array();
		foreach($m as $k => $v) {
		    $mem[$v['id']] = $v;
		}
		$tpl->set('cat', $c[0]);
		$tpl->set('experts', $mem);
		$tpl->set_file('editexpertlist.tpl.php');
		print $tpl->fetch();
		exit(0);
		break;
	    default:
		break;
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
	    $thisref['catExperts'] = $v['catExperts'];
	    $thisref['parentId'] = $v['parentId'];
	    $thisref['name'] = $v['catName'];
	    if ($v['parentId'] == 0) {
    		$list[ $v['id'] ] = &$thisref;
	    } else {
    		$refs[ $v['parentId'] ]['children'][ $v['id'] ] = &$thisref;
	    }
	}
    }
    
    $tpl->set('cat', $list);
    $tpl->set_file('listcategories.tpl.php');
}