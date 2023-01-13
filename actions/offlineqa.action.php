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

function offlineqa_action() {

    $institution = $_GET['sel1']; // strip 'i_'
    $person = intval($_GET['sel2']);

    $tpl =& Template::Instance();    
    $cfg =& Config::Instance();
    $db  =& Db::Instance();

    /*
    $members = $db->Query('SELECT m.Id, m.FirstName, m.LastName, m.UnicodeName, m.InstitutionId, i.InstitutionName FROM `starweb`.members m, `starweb`.institutions i WHERE m.InstitutionId = i.Id AND InstitutionId > 0 AND isShifter = "Y" ORDER BY m.InstitutionId, m.LastName');
    $mem = array();
    foreach($members as $k => $v) {
	$mem[$v['Id']] = $v;	
    }
    unset($members);
    */
    $mem = get_shifttable_members();
    
    $shifts = $db->Query('SELECT * FROM `ShiftSignup`.Shifts WHERE shiftTypeID = 8');
    $sharr = array();
    foreach($shifts as $k => $v) {
	$sharr[$v['week']] = $v;
    }
    unset($shifts);

    $trainings = $db->Query('SELECT * FROM `ShiftSignup`.ShiftTraining WHERE shiftTypeID = 4');
    $shtrn = array();
    foreach($trainings as $k => $v) {
			$shtrn[$v['personID']] = $v;
    }
    unset($trainings);

    $tpl->set('mem', $mem);
    $tpl->set('sharr', $sharr);

    $tpl->set('shtrn', $shtrn);

    $tpl->set('personID', $person);
    $tpl->set('personName', $mem[$person]['FirstName'].' '.$mem[$person]['LastName']);
    $tpl->set('institutionID', $mem[$person]['InstitutionId']);
    $tpl->set('institutionName', $mem[$person]['InstitutionName']);

    $tpl->set('show_member_url', $cfg->Get('generic', 'show_member_url'));

    $tpl->set('num_weeks_total', $cfg->Get('run','number_of_weeks_total'));
    $tpl->set('run_start_date', $cfg->Get('run','run_start_date'));
    $tpl->set('offline_qa_delay', $cfg->Get('generic','offline_qa_delay'));
    $tpl->set('noop_shift_week_ids', $cfg->Get('run','noop_shift_week_ids'));
    $tpl->set_file('offlineqa.tpl.php');

}