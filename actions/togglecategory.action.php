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

function togglecategory_action() {

    $tpl =& Template::Instance();    
    $cfg =& Config::Instance();
    $db  =& Db::Instance();

    $catID = intval($_POST['catID']);

    $sql = 'UPDATE `ShiftSignup`.`ShiftExpertiseCategories` SET catOperation = "'.$_POST['newVal'].'" WHERE id = '.$catID;
    $db->Query($sql);    
    $tpl->set('message', 'category display mode updated');

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