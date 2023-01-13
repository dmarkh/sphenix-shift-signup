<?php

function pnb_get_active_institutions() {
    // $url = 'https://phonebook.sdcc.bnl.gov/sphenix/service/index.php/?q=/institutions/listts/ts:1564617600/status:active';
    $url = 'https://phonebook.sdcc.bnl.gov/sphenix/service/index.php/?q=/institutions/list/status:active'.'/&rnd='.date(0);
    $inst = file_get_contents($url);
    if ( $inst === false ) { exit; }
    if ($inst) {
	$inst = json_decode($inst, true);
    }
    return $inst;
}

function pnb_get_active_members() {
    // $url = 'https://phonebook.sdcc.bnl.gov/sphenix/service/index.php/?q=/members/listts/ts:1564617600/status:active/details:compact';
    $url = 'https://phonebook.sdcc.bnl.gov/sphenix/service/index.php/?q=/members/list/status:active/details:compact'.'/&rnd='.date(0);
    $members = file_get_contents($url);
    if ( $members === false ) { exit; }
    if ($members) {
	$members = json_decode($members, true);
    }
    foreach($members as $k => $v) {
	if ( !empty( $v['fields'][85] ) && $v['fields'][85] != '0000-00-00 00:00:00' 
	    && ( ( time(0) - strtotime($v['fields'][85]) ) > 31557600 ) ) {
	    unset($members[$k]);
	}
    }
    return $members;
}

function pnb_get_active_members_full() {
    // $url = 'https://phonebook.sdcc.bnl.gov/sphenix/service/index.php/?q=/members/listts/ts:1564617600/status:active/details:full';
    $url = 'https://phonebook.sdcc.bnl.gov/sphenix/service/index.php/?q=/members/list/status:active/details:full'.'/&rnd='.date(0);
    $members = file_get_contents($url);
    if ( $members === false ) { exit; }

    if ($members) {
	$members = json_decode($members, true);
    }
    return $members;
}

function pnb_get_stats() {
    $url = 'https://phonebook.sdcc.bnl.gov/sphenix/service/index.php/?q=/service/stat'.'/&rnd='.date(0);
    $stat = file_get_contents($url);
    if ( $stat === false ) { exit; }
    if ($stat) {
			$stat = json_decode($stat, true);
    }
		// SPHENIX FIX
		foreach( $stat as $k => $v ) {
			$stat[$k]['authors'] = $stat[$k]['members'];
			$stat[$k]['shifters'] = $stat[$k]['members'];
		}
    return $stat;
}

function sort_institutions($a, $b) {
    return strcasecmp($a['name'], $b['name']);
}

function sort_members($a, $b) {
    return strcasecmp($a['LastName'], $b['LastName']);
}

function get_controls_institutions() {
		$cfg =& Config::Instance();
    $inst = pnb_get_active_institutions();
    $result = array();
    foreach( $inst as $k => $v ) { // id, name, cnt
			// disable_associate_institution_merge
			if ( $cfg->Get('run', 'disable_associate_institution_merge') != 1 ) {
				if ( !empty($v['fields'][45]) ) { continue; }
			}
			$result[] = array( 'id' => $k, 'name' => $inst[$k]['fields'][1], 'cnt' => $inst[$k]['fields'][34] );
    }
    usort($result, 'sort_institutions');
    return $result;
}

function get_expert_members() {
		$cfg =& Config::Instance();
    $inst = pnb_get_active_institutions();
    $mem = pnb_get_active_members_full();
    $result = array();
    foreach( $mem as $k => $v ) { // Id, FirstName, LastName, UnicodeName, InstitutionId
	if ( $v['fields'][43] != 'y' ) { continue; }
	// disable_associate_institution_merge
		$inst_id = $mem[$k]['fields'][17];
		if ( $cfg->Get('run', 'disable_associate_institution_merge') != 1 ) {
			$inst_id = ( $inst[ $mem[$k]['fields'][17] ]['fields'][45] ? $inst[ $mem[$k]['fields'][17] ]['fields'][45] : $mem[$k]['fields'][17] );
		}
	$result[$k] = array( 'Id' => $k, 'FirstName' => $mem[$k]['fields'][1], 'LastName' => $mem[$k]['fields'][3],
	    'Expertise' => $mem[$k]['fields'][45], 'ExpertCredit' => $mem[$k]['fields'][46],
	    'Phone' => $mem[$k]['fields'][22], 'CellPhone' => $mem[$k]['fields'][23],
	    'BnlPhone' => $mem[$k]['fields'][32], 'EmailAddress' => $mem[$k]['fields'][20],
	    'UnicodeName' => '', 'InstitutionId' => $inst_id, 'InstitutionName' => $inst[$inst_id]['fields'][1],
	    'Country' => $inst[$inst_id]['fields'][34] );
    }
    uasort($result, 'sort_members');
    return $result;
}

function get_controls_members() {
		$cfg =& Config::Instance();
    $inst = pnb_get_active_institutions();
    $mem = pnb_get_active_members();
    $result = array();
    foreach( $mem as $k => $v ) { // Id, FirstName, LastName, UnicodeName, InstitutionId
	// if ( $v['fields'][42] != 'y' ) { continue; } // SPHENIX DOES NOT USE is_shifter flag!

	// disable_associate_institution_merge
		$inst_id = $mem[$k]['fields'][17];
		if ( $cfg->Get('run', 'disable_associate_institution_merge') != 1 ) {
			$inst_id = intval( $inst[ $mem[$k]['fields'][17] ]['fields'][45] ? $inst[ $mem[$k]['fields'][17] ]['fields'][45] : $mem[$k]['fields'][17] );
		}

	$result[] = array( 'Id' => $k, 'FirstName' => $mem[$k]['fields'][1], 'LastName' => $mem[$k]['fields'][3],
	    'UnicodeName' => '', 'InstitutionId' => $inst_id, 'InstitutionName' => $inst[ $mem[$k]['fields'][17] ]['fields'][1],
	    'isShifter' => 'y', 'isAuthor' => 'y',
	    // 'isShifter' => $mem[$k]['fields'][42], 'isAuthor' => $mem[$k]['fields'][40],
	    'isExpert' => $mem[$k]['fields'][43], 'Expertise' => $mem[$k]['fields'][45], 'Phone' => $mem[$k]['fields'][22],
	    'CellPhone' => $$mem[$k]['fields'][23], 'BnlPhone' => $$mem[$k]['fields'][32], 'EmailAddress' => $mem[$k]['fields'][20]
	 );
    }
    usort($result, 'sort_members');
    return $result;
}

function get_inst_id_name_from_member($member_id) {
		$cfg =& Config::Instance();
    $inst = pnb_get_active_institutions();
    $mem = pnb_get_active_members();
    if ( empty($mem[$member_id]) ) { return array('InstitutionId' => 0, 'InstitutionName' => 'Not Found'); }
    $inst_id = $mem[$member_id]['fields'][17];
		// disable_associate_institution_merge
		if ( $cfg->Get('run', 'disable_associate_institution_merge') != 1 ) {
	    if ( !empty( $inst[$inst_id]['fields'][45] ) ) { $inst_id = $inst[$inst_id]['fields'][45]; }
		}
    return array( 'InstitutionId' => $inst_id, 'InstitutionName' => $inst[$inst_id]['fields'][1] );
}

function get_shifttable_members() {
		$cfg =& Config::Instance();
    $inst = pnb_get_active_institutions();
    $mem = pnb_get_active_members();
    $result = array();
    foreach ( $mem as $k => $v ) { // [id] = [ Id, m.FirstName, m.LastName, m.UnicodeName, m.InstitutionId, i.InstitutionName ]
	// if ( $v['fields'][42] != 'y' ) { continue; } // SPHENIX DOES NOT USE is_shifter flag!

	// disable_associate_institution_merge
		$inst_id = $mem[$k]['fields'][17];
		if ( $cfg->Get('run', 'disable_associate_institution_merge') != 1 ) {
			$inst_id = ( $inst[ $mem[$k]['fields'][17] ]['fields'][45] ? $inst[ $mem[$k]['fields'][17] ]['fields'][45] : $mem[$k]['fields'][17] );
		}

	if ( empty($inst_id) || empty($inst[$inst_id]) ) { continue; }
	$result[$k] = array( 'Id' => $k, 'member_id' => $k, 'FirstName' => $mem[$k]['fields'][1], 'LastName' => $mem[$k]['fields'][3],
	    'UnicodeName' => '',
	    'InstitutionId' => $inst_id,
	    'InstitutionName' => $inst[ $inst_id ]['fields'][1] ); 
    }
    uasort($result, 'sort_members');
    return $result;
}

function get_inst_institutions() {
	$cfg =& Config::Instance();
    $inst = pnb_get_active_institutions();
    $result = array();
    foreach( $inst as $k => $v ) {
	// disable_associate_institution_merge
			$inst_id = $mem[$k]['fields'][17];
			if ( $cfg->Get('run', 'disable_associate_institution_merge') != 1 ) {
				if ( !empty($v['fields'][45]) ) { continue; }
			}
			$result[$k]['name'] = $inst[$k]['fields'][1];
			$result[$k]['id'] = $k;
    }
    uasort($result, 'sort_institutions');
    return $result;
}

function get_inst_members() {
	$cfg =& Config::Instance();
    $inst = pnb_get_active_institutions();
    $mem = pnb_get_active_members_full();
    $result = array();
    foreach( $mem as $k => $v ) { // Id, InstitutionId, FirstName, LastName, isAuthor, isExpert, Expertise, isEmeritus	
	//if ( $v['fields'][42] != 'y' ) { continue; }

	// disable_associate_institution_merge
		$inst_id = $mem[$k]['fields'][17];
		if ( $cfg->Get('run', 'disable_associate_institution_merge') != 1 ) {
			$inst_id = ( $inst[ $mem[$k]['fields'][17] ]['fields'][45] ? $inst[ $mem[$k]['fields'][17] ]['fields'][45] : $mem[$k]['fields'][17] );
		}
	$tsdiff = 0;

	if ( !empty($v['fields'][85]) && $v['fields'][85] != '0000-00-00 00:00:00' ) { $tsdiff = time(0) - strtotime($v['fields'][85]); }

	$result[] = array( 'Id' => $k, 'InstitutionId' => $inst_id, 'FirstName' => $mem[$k]['fields'][1], 'LastName' => $mem[$k]['fields'][3], 
		// 'isAuthor' => $mem[$k]['fields'][40], 'isShifter' => $mem[$k]['fields'][42],
		'isAuthor' => 'y', 'isShifter' => 'y',
		'isExpert' => $mem[$k]['fields'][43],
	 'Expertise' => $mem[$k]['fields'][45], 'ExpertCredit' => intval($mem[$k]['fields'][46]), 'isEmeritus' => $mem[$k]['fields'][44], 'tsdiff' => $tsdiff );
    }
    usort($result, 'sort_members');
    return $result;    
}

function get_accounting_institutions() {
	$cfg =& Config::Instance();
    $inst = pnb_get_active_institutions();
    $result = array();
    foreach( $inst as $k => $v ) {
			// disable_associate_institution_merge
			if ( $cfg->Get('run', 'disable_associate_institution_merge') != 1 ) {
				if ( !empty($v['fields'][45]) ) { continue; }
			}
			$result[$k] = array( 'name' => $inst[$k]['fields'][1], 'rep_id' => $inst[$k]['fields'][9] );
    }
    uasort($result, 'sort_institutions');
    return $result;
}

