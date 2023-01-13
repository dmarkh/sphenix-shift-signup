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

function forcesignup_action() {

    require_once('actions/shifttable.action.php');                                                                                                                  
    shifttable_action();
    $tpl =& Template::Instance();
    $tpl->set('FORCE', true);

    $db  =& Db::Instance();

    /*
    $members = $db->Query('SELECT m.Id as member_id, m.FirstName, m.LastName, m.UnicodeName, i.Id as inst_id, i.InstitutionName 
    FROM `starweb`.members m, `starweb`.institutions i 
    WHERE m.InstitutionId = i.Id AND m.isShifter = "Y"  AND (m.DisabledDate = "0000-00-00 00:00:00" OR DATEDIFF(NOW(), m.DisabledDate) < 14) ORDER BY m.LastName, m.InstitutionId');
    */

    $members = get_shifttable_members();

    $sel = '<SELECT name="force_star_users" id="force_star_users"><OPTION value="">*** please select user ***</OPTION>';
    foreach($members as $k => $v) {
	$sel .= '<OPTION value="'.$v['member_id'].'">'.$v['LastName'].', '.$v['FirstName'].' - '.$v['InstitutionName'].'</OPTION>';
    }
    $sel .= '</SELECT>';
    $tpl->set('member_select', $sel);
}
