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

function updateaccounting_action() {

    $tpl =& Template::Instance();    
    $cfg =& Config::Instance();
    $db  =& Db::Instance();

    $inst = get_accounting_institutions();

    $inst_keys = implode(',', array_keys($inst));
    $inst_stat = pnb_get_stats();

    $experts = get_expert_members();
    $inst_expertcredits = array();
    foreach( $experts as $k => $v ) {
        if ( !empty($v['ExpertCredit']) ) { $inst_expertcredits[$v['InstitutionId']] += intval($v['ExpertCredit']); }
    }
                                                                                                                                                                        
    foreach( $inst_stat as $k => $v ) {
        $iid = ( $v['assoc_id'] ? intval($v['assoc_id']) : $k );
        if ( empty($inst[$iid]) ) { continue; }
        $inst[$iid]['nauth'] = ( $inst[$iid]['nauth'] ? $inst[$iid]['nauth'] + intval($inst_stat[$k]['authors']) : intval($inst_stat[$k]['authors']) );
        // $inst[$iid]['nexperts'] = ( $inst[$iid]['nexperts'] ? $inst[$iid]['nexperts'] + intval($inst_stat[$k]['experts']) : intval($inst_stat[$k]['experts']) );
	$inst[$iid]['nexperts'] += $inst_expertcredits[$k];
    }

    if (!empty($_POST['send'])) {
	$tpl->set('message', 'RECORDS UPDATED : '.date('r'));
	$scale = floatval($_POST['scale']);
	$authors = $_POST['effective_authors'];
	$shifts = $_POST['shifts_required'];
	$shifts_extra = $_POST['shifts_extra'];
	$historical_data = $_POST['historical_data'];
	$inst_keys = implode(',', array_keys($shifts));


	$experts = array();
	/*
	$exp = $db->Query('SELECT InstitutionId AS id, COUNT(*) AS nexperts FROM `starweb`.members WHERE isExpert = "Y" AND (LeaveDate > NOW() OR LeaveDate = "0000-00-00") AND InstitutionId IN ('.$inst_keys.') GROUP BY InstitutionId');
	foreach ($exp as $k => $v) {
	  # echo '->'.$v['id'].' ';
	  $experts[$v['id']]['nexperts'] = $v['nexperts'];
	}
	unset($exp);
	*/

	foreach($inst as $k => $v) {
	    $experts[ $k ]['nexperts'] = $v['nexperts'];
	}

	if ( !empty($scale) ) { // $scale != 1.0 ) {
	    foreach ( $_POST['shifts_required'] as $k => $v ) {
	      $shifts[$k]= intval( round( ($_POST['effective_authors'][$k]-$experts[$k]['nexperts']) * $scale, 0 ) );
	      if ( $shifts[$k] < 0){  $shifts[$k] = 0;}
	      # echo $k.','.$shifts[$k].','.$_POST['effective_authors'][$k].','.$experts[$k]['nexperts']."<br>";
	    }
	}
        foreach ($shifts as $k => $v) {
            $res = $db->Query('REPLACE INTO `ShiftSignup`.ShiftAdmin (institution_id, detector_experts, shifts_required, shifts_extra, effective_authors, historical_data) VALUES ('.$k.', 0, '.intval($shifts[$k]).','.intval($shifts_extra[$k]).','.intval($authors[$k]).',"'.$historical_data[$k].'")');
        }
    }


    /*
    $in = $db->Query('SELECT Id AS id, InstitutionName AS name, CouncilRepId as rep_id FROM `starweb`.institutions WHERE ISNULL(LeaveDate) = true OR LeaveDate = "0000-00-00" OR LeaveDate > NOW() ORDER BY InstitutionName ASC');    
    $inst = array();
    foreach($in as $k => $v) {
	$inst[$v['id']]['name'] = $v['name'];    
	$inst[$v['id']]['rep_id'] = $v['rep_id'];    
	$inst[$v['id']]['nauth'] = 0;
    }
    $inst_keys = implode(',', array_keys($inst));
    
    $authors = $db->Query('SELECT InstitutionId AS id, COUNT(*) AS nauth FROM `starweb`.members WHERE isAuthor = "Y" AND (LeaveDate > NOW() OR LeaveDate = "0000-00-00") AND InstitutionId IN ('.$inst_keys.')  AND (DisabledDate = "0000-00-00 00:00:00" OR DATEDIFF(NOW(), DisabledDate) < 14) GROUP BY InstitutionId');
    foreach ($authors as $k => $v) {
	$inst[$v['id']]['nauth'] = $v['nauth'];
    }

    $experts = $db->Query('SELECT InstitutionId AS id, COUNT(*) AS nexperts FROM `starweb`.members WHERE isExpert = "Y" AND (LeaveDate > NOW() OR LeaveDate = "0000-00-00") AND InstitutionId IN ('.$inst_keys.')  AND (DisabledDate = "0000-00-00 00:00:00" OR DATEDIFF(NOW(), DisabledDate) < 14) GROUP BY InstitutionId');
    foreach ($experts as $k => $v) {
	$inst[$v['id']]['nexperts'] = $v['nexperts'];
    }
    */

    $stats = $db->Query('SELECT institution_id AS id, detector_experts, shifts_required, shifts_extra, effective_authors, historical_data FROM `ShiftSignup`.ShiftAdmin WHERE institution_id IN ('.$inst_keys.')');    
    foreach($stats as $k => $v) {
	$inst[$v['id']]['defaults']['detector_experts'] = $v['detector_experts'];
	$inst[$v['id']]['defaults']['shifts_required'] = $v['shifts_required'];
	$inst[$v['id']]['defaults']['shifts_extra'] = $v['shifts_extra'];
	$inst[$v['id']]['defaults']['effective_authors'] = $v['effective_authors'];
	$inst[$v['id']]['defaults']['historical_data'] = $v['historical_data'];
    }

    $totalS = $totalP = 0;
    $ST1 = $ST2 = '';
    foreach($inst as $k => $v) {
        $totalP += $v['defaults']['effective_authors'] - intval($v['nexperts']);
        $totalS += $v['defaults']['shifts_required'];
        $ST1 .= '+'.($v['defaults']['effective_authors'] - intval($v['nexperts']));
        $ST2 .= '+'.$v['defaults']['shifts_required'];
    }

    $pswi       = explode(',', $cfg->Get('run', 'partial_shift_week_ids'        ));
    $nswi       = explode(',', $cfg->Get('run', 'noop_shift_week_ids'        ));
    $dtswi      = explode(',', $cfg->Get('run', 'disabled_training_shift_week_ids'));
    $nweeks     = intval($cfg->Get('run',       'number_of_weeks_total'         ));
    $spw        = intval($cfg->Get('generic',   'slots_per_week'                ));
    $sppw       = intval($cfg->Get('generic',   'slots_per_partial_week'        ));
    $spdtw      = intval($cfg->Get('generic',   'slots_per_disabled_training_week'));
    $oqd        = intval($cfg->Get('generic',   'offline_qa_delay'              ));
        
    // max = normal weeks + partial weeks + no training weeks + ofline qa weeks
    $max = ($nweeks - count($pswi) - count($dtswi) - count($nswi)) * $spw + count($pswi) * $sppw + count($dtswi) * $spdtw + ($nweeks - $oqd - count($nswi));
    $trainee_max = ($nweeks - count($pswi) - count($dtswi) - count($nswi)) * 6;
    $non_trainee_max = $max - $trainee_max;

    $tpl->set('non_trainee', $non_trainee_max);
    $tpl->set('inst', $inst);
    $tpl->set('scale', '');
    $tpl->set('old_scale', floatval($_POST['scale']));

    $tpl->set('totalP', $totalP);
    $tpl->set('totalS', $totalS);
    $tpl->set('ST1', $ST1);
    $tpl->set('ST2', $ST2);
    
    $tpl->set('num_weeks_total', $cfg->Get('run','number_of_weeks_total'));
    $tpl->set('run_start_date', $cfg->Get('run','run_start_date'));

    $tpl->set_file('accounting.tpl.php');
}