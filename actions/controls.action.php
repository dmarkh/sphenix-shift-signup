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

function controls_action() {

    $tpl =& Template::Instance();
    $cfg =& Config::Instance();
    $db  =& Db::Instance();

/*
    $members = $db->Query('SELECT Id, FirstName, LastName, UnicodeName, InstitutionId FROM `starweb`.members WHERE InstitutionId > 0 AND
    isShifter = "Y" ORDER BY InstitutionId, LastName');

    $institutions = $db->Query('SELECT Id as id, InstitutionName as name, Country as cnt FROM `starweb`.institutions WHERE LeaveDate > NOW() OR LeaveDate IS NULL OR
    LeaveDate = "0000-00-00" ORDER BY InstitutionName ASC');
*/

    $members = get_controls_members();
    $institutions = get_controls_institutions();

    $run_start = $cfg->Get('run', 'run_start_date');
    $run_start_seconds = strtotime($run_start);
    $nweeks = intval($cfg->Get('run', 'number_of_weeks_total'));
    $cur_time = time();
    $current_week = 0;
    for ($i = 0; $i < $nweeks; $i++) {
	$tm_st = $run_start_seconds + $i * 7 * 24 * 3600;
	if ($tm_st < $cur_time) { $current_week = $i; }
    }

    $snowing = @explode('|', $cfg->Get('generic', 'snowing'));
    if (is_array($snowing)) {
	$curtime = time();
	foreach($snowing as $k => $v) {
	    $ts = explode(',', $v);
	    $bt = strtotime($ts[0]);
	    $et = strtotime($ts[1]);
	    if ($curtime > $bt && $curtime < $et) {
		$tpl->set('snowing', '1');
		break;
	    }
	}
    }

    if (!empty($_COOKIE['inst'])) {
	$tpl->set('sel1', $_COOKIE['inst']);
    }
    if (!empty($_COOKIE['pers'])) {
	$tpl->set('sel2', $_COOKIE['pers']);
    }

    $tpl->set('inst', $institutions);
    $tpl->set('memb', $members);    
    $tpl->set('current_week', $current_week);
    
    $tpl->set('run_name', $cfg->Get('run', 'name'));
    $tpl->set_file('controls.tpl.php');
}