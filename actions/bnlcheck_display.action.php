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

function bnlcheck_display_action() {
    $cfg =& Config::Instance();
    $db  =& Db::Instance();

    if ( $cfg->Get('run', 'enable_bnl_id_check') == true && !isset($_COOKIE['labid']) ) {

	/*
            $members = $db->Query('SELECT Id, FirstName, LastName, UnicodeName, InstitutionId '                                                                        
                .'FROM `starweb`.members WHERE InstitutionId > 0 AND '                                                                                                 
                .'isShifter = "Y" ORDER BY InstitutionId, LastName');                                                                                                  
                                                                                                                                                                       
            $institutions = $db->Query('SELECT Id as id, InstitutionName as name, Country as cnt FROM `starweb`.institutions '                                         
            .'WHERE LeaveDate > NOW() OR LeaveDate IS NULL OR '                                                                                                        
            .'LeaveDate = "0000-00-00" ORDER BY InstitutionName ASC');                                                                                                 
        */

	$institutions = get_controls_institutions();
	$members = get_controls_members();
                                                                                                                                               
        $tpl =& Template::Instance();
        $tpl->set('inst', $institutions);                                                                                                                          
        $tpl->set('memb', $members); 
	$tpl->set_file('bnlcheck_display.tpl.php');
        print $tpl->fetch();
        exit(0);
    }

}
