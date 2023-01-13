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

function listexperts_action() {

    $tpl =& Template::Instance();    
    $cfg =& Config::Instance();
    $db  =& Db::Instance();

    // for each institution we should select 1), active authors 2) ex-authors, 3) non-authors.
    //$m = $db->Query('SELECT m.*, i.InstitutionName, i.Country FROM `starweb`.members m, `starweb`.institutions i WHERE m.isExpert = "Y" AND m.InstitutionId = i.Id AND (m.DisabledDate = "0000-00-00 00:00:00" or DATEDIFF(NOW(), m.DisabledDate) < 14) ORDER BY LastName ASC');
    $m = get_expert_members();    
    //print_r($m);

    $tpl->set('experts', $m);
    $tpl->set_file('listexperts.tpl.php');
}