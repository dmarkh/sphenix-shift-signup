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

function cshift_action() {

    $tpl =& Template::Instance();    
    $cfg =& Config::Instance();
    $db  =& Db::Instance();

    $week_id = intval($_GET['id']);
    $shNum = 1; // 0,1,2 = night,day,evening shifts

    $sql = 'SELECT * FROM `ShiftSignup`.`Shifts` s, `starweb`.`members` m WHERE s.week = '.$week_id.' AND s.shiftNumber = '.$shNum.' AND s.personID = m.Id ORDER BY  s.shiftTypeID ASC';
    $m = $db->Query($sql);

    $sql = 'SELECT * FROM `ShiftSignup`.`Shifts` s, `starweb`.`members` m WHERE s.week = '.$week_id.' AND s.shiftNumber = 0 AND s.shiftTypeID = 1 AND s.personID = m.Id LIMIT 1';
    $crd = $db->Query($sql);

    $m[] = $crd[0];

    $r = array();
    foreach($m as $k => $v) {
	switch($v['shiftTypeID']) {
	    case 1:
		$r['crd']['url'] = 'http://www.star.bnl.gov/';
		$r['crd']['name'] = $v['FirstName'].' '.$v['LastName'];
		$r['crd']['pic'] = 'http://www.star.bnl.gov/public/central/phonebook/'.strtolower($v['LastName']).'_'.strtolower($v['FirstName']).'.jpg';
		break;
	    case 2:
		$r['shl']['name'] = $v['FirstName'].' '.$v['LastName'];
		$r['shl']['pic'] = 'http://www.star.bnl.gov/public/central/phonebook/'.strtolower($v['LastName']).'_'.strtolower($v['FirstName']).'.jpg';
		break;
	    case 3:
		$r['do1']['name'] = $v['FirstName'].' '.$v['LastName'];
		$r['do1']['pic'] = 'http://www.star.bnl.gov/public/central/phonebook/'.strtolower($v['LastName']).'_'.strtolower($v['FirstName']).'.jpg';
		break;
	    case 4:
		$r['do2']['name'] = $v['FirstName'].' '.$v['LastName'];
		$r['do2']['pic'] = 'http://www.star.bnl.gov/public/central/phonebook/'.strtolower($v['LastName']).'_'.strtolower($v['FirstName']).'.jpg';
		break;
	    case 5:
		$r['rts']['name'] = $v['FirstName'].' '.$v['LastName'];
		$r['rts']['pic'] = 'http://www.star.bnl.gov/public/central/phonebook/'.strtolower($v['LastName']).'_'.strtolower($v['FirstName']).'.jpg';
		break;
	    case 6:
		$r['shlt']['name'] = $v['FirstName'].' '.$v['LastName'];
		$r['shlt']['pic'] = 'http://www.star.bnl.gov/public/central/phonebook/'.strtolower($v['LastName']).'_'.strtolower($v['FirstName']).'.jpg';
		break;
	    case 7:
		$r['dot']['name'] = $v['FirstName'].' '.$v['LastName'];
		$r['dot']['pic'] = 'http://www.star.bnl.gov/public/central/phonebook/'.strtolower($v['LastName']).'_'.strtolower($v['FirstName']).'.jpg';
		break;
	}
    }

    $tpl->set('m', $r);

    $tpl->set_file('cshift.tpl.php');
}