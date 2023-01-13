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

function listlogs_action() {

    $tpl =& Template::Instance();    
    $cfg =& Config::Instance();
    $db  =& Db::Instance();

    $sql = 'SELECT action_type, UNIX_TIMESTAMP(action_timestamp) as act_timestamp, origin_ip, origin_host, origin_agent, '
	. ' user_id, user_name, inst_id, inst_name, week_id, week_name, shift_slot, shift_type '
	. ' FROM `ShiftSignup`.`ShiftActions` ORDER BY action_timestamp DESC';
    $r = $db->Query($sql);

    $tpl->set('listlogs', $r);
    $tpl->set_file('listlogs.tpl.php');
}