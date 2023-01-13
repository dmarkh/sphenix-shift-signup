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

function settings_action() {

    $tpl =& Template::Instance();    
    $cfg =& Config::Instance();

    // database details    
    $tpl->set('db', $cfg->Get('main_database'));
    // database details    
    $tpl->set('db_fake', $cfg->Get('fake_database'));

    // Run details    
    $tpl->set('run', $cfg->Get('run'));

    // Generic details    
    $tpl->set('generic', $cfg->Get('generic'));

    // Generic details    
    $tpl->set('access', $cfg->Get('access'));

    // Generic details    
    $tpl->set('graph', $cfg->Get('graph'));
    

    $tpl->set_file('settings.tpl.php');
}