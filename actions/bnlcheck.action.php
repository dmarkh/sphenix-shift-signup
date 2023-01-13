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

function bnlcheck_action() {
    $cfg =& Config::Instance();

    if ( $cfg->Get('run', 'enable_bnl_id_check') == true && !isset($_COOKIE['labid']) ) {
        $tpl =& Template::Instance();
	$tpl->set_file('bnlcheck.tpl.php');
        print $tpl->fetch();
        exit(0);
    }

}
