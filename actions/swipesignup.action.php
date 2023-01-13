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

function swipesignup_action() {

    $cfg =& Config::Instance();
    $db  =& Db::Instance();

    $in = $db->Query('DELETE FROM `ShiftSignup`.`Shifts` WHERE shiftTypeID <= 5 OR shiftTypeID = 8');
    if ($db->IsError()) {
	echo $db->GetErrorMessage();
    } else {
	echo 'OK';
    }
    exit;
}