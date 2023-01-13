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

function graphs_action() {

    $tpl =& Template::Instance();    
    $cfg =& Config::Instance();
    $db  =& Db::Instance();

    $pswi 	= explode(',', $cfg->Get('run',	'partial_shift_week_ids'	));
    $nswi 	= explode(',', $cfg->Get('run',	'noop_shift_week_ids'	));
    $dtswi 	= explode(',', $cfg->Get('run',	'disabled_training_shift_week_ids'));
    $nweeks 	= intval($cfg->Get('run',	'number_of_weeks_total'		));
    $spw 	= intval($cfg->Get('generic',	'slots_per_week'		));
    $sppw 	= intval($cfg->Get('generic',	'slots_per_partial_week'	));
    $spdtw 	= intval($cfg->Get('generic',	'slots_per_disabled_training_week'));
    $oqd 	= intval($cfg->Get('generic', 	'offline_qa_delay'		));

    // max = normal weeks + partial weeks + no training weeks + ofline qa weeks
    $max = ($nweeks - count($pswi) - count($dtswi) - count($nswi)) * $spw + count($pswi) * $sppw + count($dtswi) * $spdtw + ($nweeks - $oqd - count($nswi));

    $max2 = ($nweeks - count($pswi) - count($dtswi) - count($nswi)) * ($spw - 7) + count($pswi) * ($sppw - 1) + count($dtswi) * ($spdtw - 1) + ($nweeks - $oqd - count($nswi));

    $res = array();
    
    $tm = $db->Query('SELECT min(UNIX_TIMESTAMP(entryTime)) as t_min, max(UNIX_TIMESTAMP(entryTime)) as t_max FROM `ShiftSignup`.Shifts');
    $res['time_min'] = $tm[0]['t_min'];
    $res['time_max'] = $tm[0]['t_max'];

    $rolling = $db->Query('SELECT UNIX_TIMESTAMP(a.entryTime) as tm, (SELECT COUNT(b.entryTime) from `ShiftSignup`.Shifts b WHERE b.entryTime < a.entryTime) as cnt FROM `ShiftSignup`.Shifts a ORDER BY a.entryTime ASC');

    if (!empty($rolling)) {
			foreach($rolling as $k => $v) {
	    	$res['summary_people'][] = '"'.date('r',$v['tm']).'",'.$v['cnt'];
	    	$res['percentage'][] = '"'.date('r', $v['tm']).'",'.(intval($v['cnt']/$max*100.0));
			}
//	$tpl->set('res', $res);
    }

    $rolling2 = $db->Query('SELECT UNIX_TIMESTAMP(a.entryTime) as tm, (SELECT COUNT(b.entryTime) from `ShiftSignup`.Shifts b WHERE b.entryTime < a.entryTime AND ((b.shiftTypeID > 1 AND b.shiftTypeID < 6) OR (b.shiftTypeID >= 8)) ) as cnt FROM `ShiftSignup`.Shifts a WHERE (a.shiftTypeID > 1 AND a.shiftTypeID < 6) OR (a.shiftTypeID >= 8) ORDER BY a.entryTime ASC');

    if (!empty($rolling2)) {
			foreach($rolling2 as $k => $v) {
	  	  $res['summary_people2'][] = '"'.date('r',$v['tm']).'",'.$v['cnt'];
	    	$res['percentage2'][] = '"'.date('r', $v['tm']).'",'.(intval($v['cnt']/$max2*100.0));
	    	//echo $v['cnt'].' : ';
			}
    }
    $tpl->set('res', $res);

    $start_time = strtotime($cfg->Get('run','signup_start_date_time'));

    $sql = 'SELECT UNIX_TIMESTAMP(s.entryTime) as et, s.personId as pid, s.institution as inst, m.FirstName as fn, m.LastName as ln, m.Country as mctr, i.Country as ictr from `ShiftSignup`.Shifts s, `starweb`.members m, `starweb`.institutions i 
    WHERE s.shiftTypeID != 1 AND s.entryTime > FROM_UNIXTIME('.$start_time.') AND s.personId = m.Id AND m.InstitutionId = i.Id group by personID order by entryTime asc limit 15;';

    $winners = $db->Query($sql);
    $tpl->set('winners', $winners);

    $tpl->set('width', $cfg->Get('graph', 'width'));
    $tpl->set('height', $cfg->Get('graph', 'height'));

    $tpl->set('line_color', $cfg->Get('graph', 'line_color'));
    $tpl->set('line_width', $cfg->Get('graph', 'line_width'));
    $tpl->set('fill_color', $cfg->Get('graph', 'fill_color'));
    $tpl->set('fill_alpha', $cfg->Get('graph', 'fill_alpha'));
    $tpl->set('font_angle', $cfg->Get('graph', 'font_angle'));
    $tpl->set('font_size', $cfg->Get('graph', 'font_size'));

    $tpl->set_file('graphs.tpl.php');
}