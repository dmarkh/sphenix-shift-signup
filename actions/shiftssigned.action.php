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

function shiftssigned_action() {

    $institution = $_GET['sel1']; // strip 'i_'
    $person = intval($_GET['sel2']);

    $tpl =& Template::Instance();    
    $cfg =& Config::Instance();
    $db  =& Db::Instance();

    $mem = get_shifttable_members();
    $mem = array( 0 => $mem[$person] );

    $shifts = $db->Query('SELECT * FROM `ShiftSignup`.Shifts WHERE personID = '.$person.' ORDER BY week ASC');

    $tpl->set('mem', $mem);
    $tpl->set('shifts', $shifts);
    $tpl->set('run_start_date', $cfg->Get('run','run_start_date'));
    $tpl->set_file('shiftssigned.tpl.php');
}