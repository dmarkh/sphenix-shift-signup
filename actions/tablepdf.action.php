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

function tablepdf_action() {

    $institution = $_GET['sel1']; // strip 'i_'
    $person = intval($_GET['sel2']);

    $tpl =& Template::Instance();    
    $cfg =& Config::Instance();
    $db  =& Db::Instance();

    $pswi       = explode(',', $cfg->Get('run', 'partial_shift_week_ids'        ));
    $nswi       = explode(',', $cfg->Get('run', 'noop_shift_week_ids'        ));
    $dtswi      = explode(',', $cfg->Get('run', 'disabled_training_shift_week_ids'));

    $tm = strtotime($cfg->Get('run', 'run_start_date'));
    $nweeks = intval($cfg->Get('run', 'number_of_weeks_total'));

	$mem = get_shifttable_members();

	/*
    $members = $db->Query('SELECT m.Id, m.FirstName, m.LastName, m.UnicodeName, m.InstitutionId, i.InstitutionName FROM `starweb`.members m, `starweb`.institutions i WHERE m.InstitutionId = i.Id AND m.InstitutionId > 0 AND m.isShifter = "Y"  AND (m.DisabledDate = "0000-00-00 00:00:00" OR DATEDIFF(NOW(), m.DisabledDate) < 14) ORDER BY m.InstitutionId, m.LastName');
    $mem = array();
    foreach($members as $k => $v) {
		$mem[$v['Id']] = $v;	
    }
    unset($members);
    */

    $shifts = $db->Query('SELECT * FROM `ShiftSignup`.Shifts');
    $sharr = array();
    $taken_by_person = array();
    foreach($shifts as $k => $v) {
	$sharr[$v['week']][$v['shiftNumber']][$v['shiftTypeID']] = $v;
	if ($v['personID'] == $person) {
	    $taken_by_person[$v['week']] = 1;
	}
    }
    unset($shifts);

    foreach($nswi as $k => $v) {
	for ($j = 0; $j < 3; $j++) {
	for ($i = 1; $i < 8; $i++) {
	    $sharr[$v][$j][$i] = 'N/A';
	}
	}
    }

    foreach($pswi as $k => $v) {
	for ($j = 0; $j < 3; $j++) {
	for ($i = 4; $i < 8; $i++) {
	    $sharr[$v][$j][$i] = 'N/A';
	}
	}
    }
    foreach($dtswi as $k => $v) {
	for ($j = 0; $j < 3; $j++) {
	for ($i = 6; $i < 8; $i++) {
	    $sharr[$v][$j][$i] = 'N/A';
	}

	}
    }

    $pdf = new PDF_TB('l');
    //$pdf->SetMyPageBreak('cont-d on next page..');
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Times','B',16);
    $pdf->Cell(0,10,'SPHENIX Shift Sign-up Table for '.$cfg->Get('run', 'name'),0,1,'C');
    $pdf->SetFont('Times','B',14);
    $pdf->Cell(0,10,'as of '.date('r'),0,1,'C');
    $pdf->SetFont('Times','B',12);
    // $pdf->SetWidths(array(20, 35, 20, 30, 35, 35, 35, 35, 35));
    $pdf->SetWidths(array(30, 45, 30, 40, 45, 45, 45));

    //$pg = 0;
    //$pg_max = 2;
    for ($k = 0; $k < $nweeks; $k++) {
		if ( in_array($k, $nswi) ) { continue; }
	$pdf->CheckPageBreak(36);
	$v = $sharr[$k];
	//if ($pg > $pg_max) { $pg = 0; $pg_max = 3; $pdf->AddPage(); }
	//$pg++;
	$pdf->SetFont('Arial','B',10);
	// $pdf->Row(array('WEEK', 'COORDINATOR', 'SHIFT', 'SH.LEADER', 'DETECTOR OPR.', 'DAQ OPR.', 'DATA MON. OPR.', 'LEADER TRAINEE', 'D.O.TRAINEE'), true);
	$pdf->Row(array('WEEK', 'COORDINATOR', 'SHIFT', 'SH.LEADER', 'DETECTOR OPR.', 'DAQ OPR.', 'DATA MON. OPR.'), true);
	$pdf->SetFont('Arial','',9);
	$tm_st = $tm + $k * 7 * 24 * 3600;
	$tm_en = $tm + ($k+1) * 7 * 24 * 3600;
	for ($i = 0; $i < 3; $i++) {
	    $crd = '';
	    $week = '';
	    if ($i == 1) { 
		$crd = $mem[$v[0][1]['personID']]['FirstName'].' '.$mem[$v[0][1]['personID']]['LastName']; 
		$week = date('M jS', $tm_st).' - '.date('M jS', $tm_en); 
	    } 
	    $sh_l = '';
	    $det_1 = ''; $det_2 = ''; $crew = ''; $shlt = ''; $dot = '';
	    if (!empty($v[$i][2]['personID'])) {
		$sh_l = $mem[$v[$i][2]['personID']]['FirstName'].' '.$mem[$v[$i][2]['personID']]['LastName'];
	    }
	    if (!empty($v[$i][3]['personID'])) {
		$det_1 = $mem[$v[$i][3]['personID']]['FirstName'].' '.$mem[$v[$i][3]['personID']]['LastName'];
	    }
	    if (!empty($v[$i][4]['personID'])) {
		$det_2 = $mem[$v[$i][4]['personID']]['FirstName'].' '.$mem[$v[$i][4]['personID']]['LastName'];
	    }
	    if (!empty($v[$i][5]['personID'])) {
		$crew = $mem[$v[$i][5]['personID']]['FirstName'].' '.$mem[$v[$i][5]['personID']]['LastName'];
	    }
	    if (!empty($v[$i][6]['personID'])) {
		$shlt = $mem[$v[$i][6]['personID']]['FirstName'].' '.$mem[$v[$i][6]['personID']]['LastName'];
	    }
	    if (!empty($v[$i][7]['personID'])) {
		$dot = $mem[$v[$i][7]['personID']]['FirstName'].' '.$mem[$v[$i][7]['personID']]['LastName'];
	    }
	    $line = false;
	    if ($i == 0) { $line = true; }
	    $pdf->Row(
		array($week, $crd, get_shift_hours($i), 
		    $sh_l, $det_1, $det_2, $crew, 
		    // $shlt, $dot
		),
		$line
	    );
	}
	$pdf->Cell(0,5, '', 'T', 1, 'L');    
    }

    $pdf->Cell(0,10, '*** THE END ***', 'T+B', 1, 'C');
    $file_name = 'shift-signup-table-'.date("Y-m-d").'.pdf';
    $pdf->Output($file_name, 'I');

}