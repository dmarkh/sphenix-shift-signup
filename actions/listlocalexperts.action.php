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

function listlocalexperts_action() {

    $tpl =& Template::Instance();    
    $cfg =& Config::Instance();
    $db  =& Db::Instance();


//    $m = $db->Query('SELECT * FROM `ShiftSignup`.ShiftExperts WHERE 1 ORDER BY exLastName ASC');    
//    $m = $db->Query('SELECT a.*, b.ExpertCredit as ecred FROM `ShiftSignup`.`ShiftExperts` a, `starweb`.`members` b WHERE a.exID = b.ID');
    $experts = get_expert_members();                                                                                                                                    
                                                                                                                                                                        
    $m = $db->Query('SELECT * FROM `ShiftSignup`.ShiftExperts WHERE 1 ORDER BY exLastName ASC');                                                                        
    //$m = $db->Query('SELECT a.*, b.ExpertCredit as ecred FROM `ShiftSignup`.`ShiftExperts` a, `starweb`.`members` b WHERE a.exID = b.ID ORDER BY a.exLastName ASC');  
    foreach($m as $k => $v) {                                                                                                                                           
        if ( !empty($experts[ $v['exID'] ]['ExpertCredit']) ) {                                                                                                         
            $m[$k]['ExpertCredit'] = intval($experts[ $v['exID'] ]['ExpertCredit']);                                                                                    
            $m[$k]['ecred'] = intval($experts[ $v['exID'] ]['ExpertCredit']);                                                                                           
        } else {
            $m[$k]['ExpertCredit'] = 0;
            $m[$k]['ecred'] = 0;
	}
    }

    $ex = array();
    foreach($m as $k => $v) {
	$ex[$v['id']] = &$m[$k]; 
    }

    $cat = $db->Query('SELECT * FROM `ShiftSignup`.ShiftExpertiseCategories WHERE 1 ORDER BY catWeight ASC');    
    $refs = array();
    $list = array();
    if (!empty($cat) && is_array($cat)) {
        foreach($cat as $k => $v) {
            $thisref = &$refs[ $v['id'] ];
            $thisref['id'] = $v['id'];
	    if (!empty($v['catExperts'])) {
		$e = array_filter(explode(',', $v['catExperts']));
		foreach($e as $k2 => $v2) {
		    $thisref['persons'][] = &$ex[$v2];
		}
	    }
            $thisref['w'] = $v['catWeight'];
            $thisref['parentId'] = $v['parentId'];
            $thisref['catName'] = $v['catName'];
            $thisref['catOperation'] = $v['catOperation'];
            if ($v['parentId'] == 0) {
                $list[ $v['id'] ] = &$thisref;
            } else {
                $refs[ $v['parentId'] ]['children'][ $v['id'] ] = &$thisref;
            }
        }
    }


    foreach($list as $k => $v) {
	if (empty($v['persons']) && !empty($v['children'])) {
	    foreach($v['children'] as $k2 => $v2) {
		if (empty($v2['persons']) && empty($v2['children'])) {
		    unset($list[$k]['children'][$k2]);
		}
	    }
	}
	if (empty($v['persons']) && empty($list[$k]['children'])) {
	    unset($list[$k]);
	}
    }

    if ( !empty($_GET['pdf']) || !empty($_POST['pdf']) ) { 
	  get_pdf_version($list);
	  exit(0);
    }

    $tpl->set('cat', $list);

	if ( !empty($_GET['json']) ) {
	  $tpl->set_file('listlocalexperts.json.tpl.php');
	} else {
  	  $tpl->set_file('listlocalexperts.tpl.php');
	}
}

function get_pdf_version(&$cat) {
    require_once('fpdf/fpdf_tb.php');
    $cfg =& Config::Instance();

    $pdf = new PDF_TB('l');    

    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Times','B',16);
    $pdf->Cell(0,10,'STAR Experts List for '.$cfg->Get('run', 'name'),0,1,'C');
    $pdf->SetFont('Times','',8);
    $pdf->Cell(0,5,'as of '.date('r'),0,1,'C');
    $pdf->SetFont('Times','B',10);
    $pdf->SetWidths(array(100, 60, 60, 60));
    $pdf->SetAligns(array('L','C', 'C', 'C'));

    foreach($cat as $k => $v) {
	$pdf->Cell(0,5, '', 'B', 1, 'L');
	$pdf->SetFont('Arial','BI',10);
        $pdf->Row(array($v['catName'], 'Primary Phone', 'Cell Phone', 'Home Phone'));
        $pdf->Row(array('', '', 'OR ', ' E-Mail'));
	$pdf->SetFont('Times','',9);
        if (!empty($v['persons'])) {
    	    foreach ($v['persons'] as $k2 => $v2) {
		if ($v2['exDisabled'] != 0) { continue; }
		$prs = '';
		if ($v['catOperation'] == 'or') { 
		    $prs .= '* '; 
		} else { 
		    $prs .= ($k2+1).'. '; 
		};
		$prs .= $v2['exFirstName'].' '.$v2['exLastName'];
                if (empty($v2['exPhoneCell']) && empty($v2['exPhoneHome'])) {
		    $pdf->Row(array($prs, $v2['exPhonePrimary'], '', $v2['exEmail']));
                } else {
		    $pdf->Row(array($prs, $v2['exPhonePrimary'], $v2['exPhoneCell'], $v2['exPhoneHome']));
                }
		if (!empty($v2['exDescription'])) {
		    $pdf->Row(array('', '', '', $v2['exDescription']));
		}

	    }    
	}

	if ( !empty($v['children'])) {
    	    foreach($v['children'] as $k2 => $v2) {
        	dump_child_category_pdf($pdf, $v2);
    	    }
	}
    
	//$pdf->Cell(0,10,'',0,1,'C'); // empty line after each category
    }

    $file_name = 'run-experts-table-'.gmdate("Y-m-d", time()).'.pdf';
    $pdf->Output($file_name, 'I');

}

function dump_child_category_pdf(&$pdf, &$v, $prevName = '') {
    if (!empty($v['persons'])) {
	$ind = '';
        foreach ($v['persons'] as $k2 => $v2) {
	if ($v['catOperation'] == 'or') { $ind = '*'; } else { $ind = ($k2+1).'. '; };
	    $prs = $ind.' '.strtoupper($prevName.':'.$v['catName'])."   -   ".$v2['exFirstName'].' '.$v2['exLastName'];
                if (empty($v2['exPhoneCell']) && empty($v2['exPhoneHome'])) {
		    $pdf->Row(array($prs, $v2['exPhonePrimary'], '', $v2['exEmail']));
                } else {
		    $pdf->Row(array($prs, $v2['exPhonePrimary'], $v2['exPhoneCell'], $v2['exPhoneHome']));
                }
		if (!empty($v2['exDescription'])) {
		    $pdf->Row(array('', '', '', $v2['exDescription']));
		}
        }
    }
    if ( !empty($v['children'])) {
        foreach($v['children'] as $k2 => $v2) {
            dump_child_category_pdf($pdf, $v2, $prevName.':'.$v['catName']);
        }
    }
}


