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

class TableSorter {
  protected $column;
  function __construct($column) {
    $this->column = $column;
  }
  function sort($table) {
    usort($table, array($this, 'compare'));
    return $table;
  }
  function compare($a, $b) {
    if ($a[$this->column] == $b[$this->column]) {
      return 0;
    }
    return ($a[$this->column] > $b[$this->column]) ? -1 : 1;
  }
}

function rejectiontest_action() {
    $err = '';
    $warn = '';

    $par = array();
    $par['I'] = $_POST['par_I'] ? $_POST['par_I'] : 5;
    $par['T'] = $_POST['par_T'] ? $_POST['par_T'] : 2;
    $par['P'] = $_POST['par_P'] ? $_POST['par_P'] : 4;
    $par['M'] = $_POST['par_M'] ? $_POST['par_M'] : 33;

    $tpl =& Template::Instance();    
    $cfg =& Config::Instance();
    $db  =& Db::Instance();

//    $rej = $db->Query('SELECT entryTime, authors, ip, params, isSent FROM `ShiftSignup`.`ShiftRejection` ORDER BY entryTime DESC');

    // count all required shifts from ShiftsAdmin table
    $req = $db->Query('SELECT SUM(shifts_required) AS req FROM `ShiftSignup`.`ShiftAdmin`');

    // count all required shifts from ShiftsAdmin table
    $shifts = array();
    $tst = $db->Query('SELECT * FROM `ShiftSignup`.`ShiftAdmin`');
    foreach($tst as $k => $v) {
	unset($v['detector_experts']);
	unset($v['effective_authors']);
	$shifts[$v['institution_id']] = $v;
    }

    // get all alive institutions
    $in = $db->Query('SELECT Id AS id, InstitutionName AS name FROM `starweb`.institutions WHERE (ISNULL(LeaveDate) = true OR LeaveDate > NOW() OR LeaveDate = 0) AND CouncilRepId IS NOT NULL ORDER BY InstitutionName ASC');
    $inst = array();
    foreach($in as $k => $v) {
	$inst[$v['id']]['name'] = $v['name'];
	$inst[$v['id']]['shifts_required'] = $shifts[$v['id']]['shifts_required'];

	$tmp = explode(';', trim($shifts[$v['id']]['historical_data'], "; \n\t\r"));
	$inst[$v['id']]['historical_data'] = $tmp;
	foreach($tmp as $k2 => $v2) {
	    $tmp2 = explode(',',$v2);
	    if (floatval($tmp2[0]) > 0) {
		$tmp3 = round( ( floatval($tmp2[0]) - floatval($tmp2[1]) ) / floatval( $tmp2[0] ) * 100.0, 2) ;
		if ($tmp3 < 0) $tmp3 = 0;
		$inst[$v['id']]['hist'][] = $tmp3;
	    } else {
		$inst[$v['id']]['hist'][] = 0;
	    }
	}
    }

    unset($in);
    $inst_keys = implode(',',array_keys($inst));

    $taken = $db->Query('SELECT institution_id, COUNT(*) AS tkn FROM `ShiftSignup`.Shifts WHERE personID > 0 GROUP BY institution_id');
    foreach($taken as $k => $v) {
	$inst[$v['institution_id']]['shifts_taken'] = $v['tkn'];
    }

    // for each institution we should select 1), active authors 2) ex-authors, 3) non-authors.
    $m = $db->Query('SELECT Id, InstitutionId, FirstName, LastName, isAuthor, isExpert, Expertise, isEmeritus, UNIX_TIMESTAMP(NOW()) as tsnow, UNIX_TIMESTAMP(LeaveDate) as tsld, (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(LeaveDate)) as tsdiff, DATEDIFF(NOW(), DisabledDate) as disdiff FROM `starweb`.members WHERE InstitutionId IN ('.$inst_keys.') ORDER BY LastName ASC');
    
    foreach($m as $k => $v) {
	if (strtolower($v['isAuthor']) == 'y') {
	    // active author?
	    if (intval($v['disdiff']) > 0) {
		//echo $v['FirstName'].' '.$v['LastName'].'<br>';
		// disabled author, essentially non-author  
		$inst[$v['InstitutionId']]['notauthor'][$v['Id']] = $v['FirstName'].' '.$v['LastName'];
	    } else if (intval($v['tsld']) == 0) {
		// yes, active
		if (strtolower($v['isExpert']) == 'y') {
		    $inst[$v['InstitutionId']]['experts'][$v['Id']] = '<a href="javascript:void(0);" onmouseover="return overlib(\'Expert: '.$v['Expertise'].'\');" onmouseout="return nd();"><b>'.$v['FirstName'].' '.$v['LastName'].'</b></a>';
		} else if (strtolower($v['isEmeritus']) == 'y') {
		    // will save as underline author - makes no sens of not an author
		    $inst[$v['InstitutionId']]['active'][$v['Id']] = '<u>'.$v['FirstName'].' '.$v['LastName'].'</u>';
		} else {
		    $inst[$v['InstitutionId']]['active'][$v['Id']] = $v['FirstName'].' '.$v['LastName'];
		}
	    } else if ($v['tsdiff'] < (365 * 24 * 3600) ) {
		// left collaboration, but still in author list
		$inst[$v['InstitutionId']]['passive'][$v['Id']] = $v['FirstName'].' '.$v['LastName'];
	    } else {
		//echo 'author, but left long ago';
	    }
	} else {
	    if (intval($v['tsld']) == 0) {
		// valid, not author
		$inst[$v['InstitutionId']]['notauthor'][$v['Id']] = $v['FirstName'].' '.$v['LastName'];
	    } else {
		// echo 'non-author, left';
	    }
	}
    }


    foreach($inst as $k => $v) {
//	list ($inst[$k]['shifts_required'],$inst[$k]['shifts_taken']) = explode(',',$inst[$k]['historical_data'][0]);
	$v['shifts_taken'] = $inst[$k]['shifts_taken'];
//	$v['shifts_required'] = $inst[$k]['shifts_required'];
    
	$inst[$k]['active_authors'] = count($v['active']) + count($v['experts']);

	    if ($v['shifts_required'] > 0) {
	        $r = round($v['shifts_taken'] / $v['shifts_required'], 2);
	        if ($r > 1.0) { $r = 1.0; }
	        $inst[$k]['r'] = $r;
	    } else {
	        $inst[$k]['r'] = 1.0;
	    }
	    // missed percentage
	    $inst[$k]['pct_miss'] = (1.0 - $inst[$k]['r']) * 100.0; 
	    // D raw
	    $D = $inst[$k]['active_authors'] * ( $inst[$k]['pct_miss'] / 100.0 );
	    if ($D > 0) {
		$inst[$k]['D'] = round($D,2);
	    } else {
		$inst[$k]['D'] = 0;
	    };
	    // D Final
	    $DF = $D;
	    if ($DF > 0 && $DF < 1) {
		$DF = 1;
		$inst[$k]['DF'] = $DF;
	    } else if ($DF <= 0) {
		$inst[$k]['DF'] = 0;
	    } else {
		$inst[$k]['DF'] = floor($DF);
	    }
    }

//    print_r($inst);


    // I = number of institutions in top 
    // T = times over a period of ..
    // P = .. period of years 
    // M = allowed percentage of shift dues not taken by collaboration

    if ($par['T'] > $par['P']) { $par['T'] = $par['P']; }
    $topX = array();
    foreach ($inst as $k => $v) {
	$inst[$k]['sort'] = 0;    
	if ($v['pct_miss'] < $par['M']) {
	   // do nothing, not guilty this year..
	    $inst[$k]['sort'] = $v['D'] + $v['D']*($v['pct_miss']/100.0);
	    $inst[$k]['color'] = '#EEE';
	} else {
	    $inst[$k]['sort'] = $v['D'] + $v['D']*($v['pct_miss']/100.0);
	    $inst[$k]['color'] = 'cyan';
	    // ok, candidate to punishment
	    // check historical data:
	    $bad_years = 1;
	    $ctr = 1;
	    foreach($inst[$k]['hist'] as $k2 => $v2) {
		if ($v2 >= $par['M']) { $bad_years += 1; }
		$ctr += 1;
		if ($ctr >= $par['P']) break; // past X years scan only, or whatever history inst has..
	    }
	    if ($bad_years > $par['T']) {
		$inst[$k]['bad_years'] = $bad_years;
		$inst[$k]['color'] = 'pink';
		// ok, enough bad years found, mark for sort:
		if ($inst[$k]['sort'] > 0) {
		    $topX[$k] = $inst[$k]['sort'];
		}
	    }
	}
    }
    arsort($topX);
    $topx = array_slice($topX, 0, $par['I'], true);

    $punished = array();
    foreach($topx as $k => $v) {
	// calculate authors to be rejected:
	$people = $inst[$k]['active'] ? $inst[$k]['active'] : array(); 
	$removed = array();
	if ( !empty($inst[$k]['experts']) ) {
	    $people = array_merge($people, $inst[$k]['experts']);
	};
	$ppl_idx = array_keys($people);
	shuffle($ppl_idx);
	for ($i = 0; $i < $inst[$k]['DF']; $i++) {
	    $idx = array_pop($ppl_idx);
	    $removed[$idx] = $people[$idx];
	}
	$inst[$k]['removed_authors'] = $removed;
	$punished[$k]['removed'] = $removed;
	$punished[$k]['inst_name'] = $inst[$k]['name'];
    }
    $tmp = $inst;
    $rmv = array();
    foreach($punished as $k => $v) {
	$rmv[$k] = $inst[$k];
	unset($tmp[$k]);
    }
    foreach($tmp as $k => $v) {
	$rmv[$k] = $v;
    }

//    print_r($rej);

    $sorter = new TableSorter('sort'); // sort by first column
    $rmv = $sorter->sort($rmv);

    $tpl->set('inst', $rmv);
    $tpl->set('punished', $punished);
    $tpl->set('par', $par);
    $tpl->set('err', $err);
    if (!empty($rej)) {
	$tpl->set('rej', $rej);
    }
    $tpl->set('warn', $warn);
    $tpl->set_file('rejectiontest.tpl.php');
}

function get_historical_data($source) {

    $db  =& Db::Instance($source);

    $inst = array();

    // count number of taken shifts per institution
    $tks = $db->Query('SELECT COUNT(*) AS taken, i.Id AS institution_id, i.InstitutionName FROM `ShiftSignup`.`Shifts` s, `starweb`.`members` m, `starweb`.`institutions` i'
	    .' WHERE s.personID = m.Id AND m.InstitutionId = i.Id GROUP BY i.InstitutionName');

    if (!empty($tks)) {
    foreach($tks as $k => $v) {
	$inst[$v['institution_id']]['shifts_taken'] = $v['taken'];
    }    
    }

    // get all required shifts and effective authors per institution
    $tks = $db->Query('SELECT institution_id, shifts_required, effective_authors FROM `ShiftSignup`.`ShiftAdmin`');
    foreach($tks as $k => $v) {
	$inst[$v['institution_id']]['shifts_required'] = $v['shifts_required'];
	$inst[$v['institution_id']]['effective_authors'] = $v['effective_authors'];
    }    

    $tks = $db->Query('SELECT Id AS id, InstitutionName AS name FROM `starweb`.institutions WHERE ( ISNULL(LeaveDate) = true OR LeaveDate > NOW() OR LeaveDate = 0 ) AND (DisabledDate = "0000-00-00 00:00:00" OR DATEDIFF(NOW(), DisabledDate) < 14)  ORDER BY InstitutionName ASC');
    foreach($tks as $k => $v) {
	$inst[$v['id']]['name'] = $v['name'];
    }

    $inst_keys = array_keys($inst);
    // for each institution we should select 1), active authors 2) ex-authors, 3) non-authors.
    $sql = "SELECT Id, InstitutionId, FirstName, LastName, isAuthor, "
	." UNIX_TIMESTAMP(NOW()) as tsnow, UNIX_TIMESTAMP(LeaveDate) as tsld, "
	." (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(LeaveDate)) as tsdiff "
	." FROM `starweb`.members WHERE InstitutionId IN (".implode(',',$inst_keys).") AND (DisabledDate = '0000-00-00 00:00:00' OR DATEDIFF(NOW(), DisabledDate) < 14) ORDER BY LastName ASC";
    $tks = $db->Query($sql);
    if (!empty($tks)) { 
    foreach($tks as $k => $v) {
	if (strtolower($v["isAuthor"]) == "y") {
	    // active author?
	    if (intval($v["tsld"]) == 0) {
		// yes, active
		$inst[$v['InstitutionId']]['active_authors'] = $inst[$v['InstitutionId']]['active_authors'] ? ($inst[$v['InstitutionId']]['active_authors']+1) : 1;
	    }
	}
    }
    }

    foreach($inst as $k => $v) {
	if (empty($v['shifts_required'])) { $inst[$k]['shifts_required'] = 0; }
	if (empty($v['shifts_taken'])) { $inst[$k]['shifts_taken'] = 0; }
	if (empty($v['active_authors'])) { $inst[$k]['active_authors'] = 0; }
	if (empty($v['name'])) { unset($inst[$k]); }
	if (!empty($v['effective_authors'])) { unset($inst[$k]['effective_authors']); }
    }
    return $inst;
}


