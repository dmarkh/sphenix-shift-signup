<?php


error_reporting( E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT );

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

ini_set('memory_limit','32M');

require_once('./util/template.util.php');
require_once('./util/compat.util.php');
require_once('./util/config.util.php');
require_once('./util/email.util.php');
require_once('./util/db.util.php');
require_once('./util/trainreq.util.php');
require_once('./util/pnb.api.php');

$cfg =& Config::Instance();
$tpl =& Template::Instance();

function present_captcha() {
    unset($_SESSION['secure']);
    include_once('captcha/captcha.php');
}

// captcha check
if ( $cfg->Get('run', 'display_captcha') == true ) {
    session_start();                                                                                                                                               
    $session_id = session_id();
    if ( !empty($_SESSION['security_number']) && !empty($_POST['secure']) ) { // captcha posted first time..
	if ( md5(sha1($_POST['secure'].$session_id,false),false) != $_SESSION['security_number'] ) { // captcha test failed, take again
	    present_captcha(); exit;	    
	} else { // captcha test succeeded
	    $_SESSION['secure'] = $_POST['secure']; // move answer to session var
	}
    } else if ( !empty($_SESSION['security_number']) && !empty($_SESSION['secure']) ) { // captcha kept in session..
	if ( md5(sha1($_SESSION['secure'].$session_id,false),false) != $_SESSION['security_number'] ) { // captcha test failed, take again
	    present_captcha(); exit;	    	    
	} else { // captcha test succeeded
	    // do nothing, just pass through..
	}
    } else { // present new captcha otherwise
	    present_captcha(); exit;
    }
}

if ( $cfg->Get('run', 'enable_bnl_id_check') == true && ( !isset($_COOKIE['labid']) || !empty($_GET['ignore']) ) && $_GET['do'] == 'bnlcheck_check') {
    require_once('actions/bnlcheck_check.action.php');
    bnlcheck_check_action();
    if (!empty($_GET['ignore'])) { exit; }
} else if ( $cfg->Get('run', 'enable_bnl_id_check') == true && !isset($_COOKIE['labid']) && $_GET['do'] == 'bnlcheck_display') {
    require_once('actions/bnlcheck_display.action.php');
    bnlcheck_display_action();
    exit;
} else if ( $cfg->Get('run', 'enable_bnl_id_check') == true && !isset($_COOKIE['labid']) ) {
    require_once('actions/bnlcheck.action.php');
    bnlcheck_action();
    exit;
}

// countdown timer check
if ( $cfg->Get('run', 'enable_signup_start_countdown') == true ) {
    $allowed_ips = explode(',', $cfg->Get('access', 'allowed_ip_addresses'));
    if (!in_array(strval(get_ip_address()), $allowed_ips)) { // get_ip_address is taught to ignore 192.168.XX.YY - proxy ip
	$opening_time = strtotime($cfg->Get('run','signup_start_date_time'));
	$current_time = time();
	if ($current_time < $opening_time) {
	    if (!empty($_COOKIE['pers'])) {
		$db  =& Db::Instance();
    	        $query = 'SELECT * FROM `starweb`.`members` WHERE Id = '.intval($_COOKIE['pers']).' LIMIT 1';                                                                             
	        $res = $db->Query($query);                                                                                                                                     
		if (!empty($res) && !empty($res[0]['FirstName']) && !empty($res[0]['LastName'])) { 
	            $fname = $res[0]['FirstName'];                                                                                                                     
    		    $lname = $res[0]['LastName']; 
		    $tpl->set('preset_name', $fname.' '.$lname);
		}
	    }
	    $tpl->set('time_diff', ($opening_time - $current_time));
	    $tpl->set_file('countdown.tpl.php');
	    print $tpl->fetch();
	    exit(0);
	}
    }
}

// processing of 'actions' (Front Controller)
$action = '';
if (!empty($_POST['do'])) {
    if ($_POST['do'] == 'process_auth' && !empty($_POST['former_do'])) {
	$action = $_POST['former_do'];
    } else {
	$action = $_POST['do'];
    }
} else if (!empty($_GET['do'])) {
    $action = $_GET['do'];
}


if (empty($action)) {
    // output front page
    $tpl->set_file('frontpage.tpl.php');
    print $tpl->fetch();
    exit(0);
}


// process specific action by invoking specific controller code
switch($action) {
    // intro table, with welcome text. See /templates/intro.body.tpl.php for details
    case 'introtxt':
	$tpl->set_file('intro.tpl.php');
	break;
    // control frame (top frame)
    case 'controls':
	require_once('actions/controls.action.php');
	controls_action();
	break;
    // shift table display (bottom frame)
    case 'shifttable':
	require_once('actions/shifttable.action.php');
	shifttable_action();
	break;
    // shifts signed by person (bottom frame)
    case 'shiftssigned':
	require_once('actions/shiftssigned.action.php');
	shiftssigned_action();
	break;
    // shifts signed by person (bottom frame)
    case 'cshift':
	require_once('actions/cshift.action.php');
	cshift_action();
	break;
    // offline qa list (bottom frame)
    case 'offlineqa':
	require_once('actions/offlineqa.action.php');
	offlineqa_action();
	break;
    // graphs: shift-signup dynamics
    case 'graphs':
	require_once('actions/graphs.action.php');
	graphs_action();
	break;
    // settings: view configuration file details..
    case 'settings':
	require_once('actions/settings.action.php');
	settings_action();
	break;
    // institutions - shifts statistics
    case 'institutions':
	require_once('actions/institutions.action.php');
	institutions_action();
	break;
    case 'controlcenter':
	require_once('actions/controlcenter.action.php');
	controlcenter_action();
	break;
    case 'accounting':
	require_once('util/auth.util.php');
	$a = new ShiftAuth();
	$a->check_post();
	require_once('actions/accounting.action.php');
	accounting_action();
	break;
    case 'stats':
	//require_once('util/auth.util.php');
	//$a = new ShiftAuth();
	//$a->check_post();
	require_once('actions/stats.action.php');
	stats_action();
	break;
    // edit training records for selected person
    case 'edittraining':
	require_once('util/auth.util.php');
	$a = new ShiftAuth();
	$a->check_post();
	require_once('actions/edittraining.action.php');
	edittraining_action();
	break;
    // update training records for selected person
    case 'updatetraining':
	require_once('util/auth.util.php');
	$a = new ShiftAuth();
	$a->check_post();
	require_once('actions/updatetraining.action.php');
	updatetraining_action();
	break;
    // update accounting records
    case 'updateaccounting':
	require_once('util/auth.util.php');
	$a = new ShiftAuth();
	$a->check_post();
	require_once('actions/updateaccounting.action.php');
	updateaccounting_action();
	break;
    // update accounting records
    case 'listexperts':
	//require_once('util/auth.util.php');
	//$a = new ShiftAuth();
	//$a->check_post();
	require_once('actions/listexperts.action.php');
	listexperts_action();
	break;
    case 'manageexperts':
	require_once('util/auth.util.php');
	$a = new ShiftAuth();
	$a->check_post();
	require_once('actions/manageexperts.action.php');
	manageexperts_action();
	break;
    case 'listcategories':
	require_once('util/auth.util.php');
	$a = new ShiftAuth();
	$a->check_post();
	require_once('actions/listcategories.action.php');
	listcategories_action();
	break;
    case 'modifycategory':
	require_once('util/auth.util.php');
	$a = new ShiftAuth();
	$a->check_post();
	require_once('actions/modifycategory.action.php');
	modifycategory_action();
	break;
    case 'addexpert':
	require_once('util/auth.util.php');
	$a = new ShiftAuth();
	$a->check_post();
	require_once('actions/addexpert.action.php');
	addexpert_action();
	break;
    case 'removeexpert':
	require_once('util/auth.util.php');
	$a = new ShiftAuth();
	$a->check_post();
	require_once('actions/addexpert.action.php');
	addexpert_action();
	break;
    case 'togglecategory':
	require_once('util/auth.util.php');
	$a = new ShiftAuth();
	$a->check_post();
	require_once('actions/togglecategory.action.php');
	togglecategory_action();
	break;
    case 'importexperts':
	require_once('util/auth.util.php');
	$a = new ShiftAuth();
	$a->check_post();
	require_once('actions/importexperts.action.php');
	importexperts_action();
	break;
    case 'updateexperts':
	require_once('util/auth.util.php');
	$a = new ShiftAuth();
	$a->check_post();
	require_once('actions/updateexperts.action.php');
	updateexperts_action();
	break;
    case 'listlocalexperts':
	require_once('actions/listlocalexperts.action.php');
	listlocalexperts_action();
	break;
    // user submitted desired signup slots (add or remove)
    case 'submitsignup':
	require_once('actions/submitsignup.action.php');
	submitsignup_action();
	break;
    // user submitted desired signup slots (add or remove)
    case 'finalizesignup':
	require_once('actions/finalizesignup.action.php');
	finalizesignup_action();
	break;
    case 'tablepdf':
	require_once('fpdf/fpdf_tb.php');
	require_once('actions/tablepdf.action.php');
	tablepdf_action();
	break;
    case 'tablepdfcur':
	require_once('fpdf/fpdf_tb.php');
	require_once('actions/tablepdfcur.action.php');
	tablepdfcur_action();
	break;
    case 'configoverrides':
	require_once('util/auth.util.php');
	$a = new ShiftAuth();
	$a->check_post();
	require_once('actions/configoverrides.action.php');
	configoverrides_action();
	break;
    case 'listlogs':
	require_once('util/auth.util.php');
	$a = new ShiftAuth();
	$a->check_post();
	require_once('actions/listlogs.action.php');
	listlogs_action();
	break;
    case 'rejection':
	require_once('util/auth.util.php');
	$a = new ShiftAuth();
	$a->check_post();
	require_once('actions/rejection.action.php');
	rejection_action();
	break;
    case 'rejectiontest':
	require_once('util/auth.util.php');
	$a = new ShiftAuth();
	$a->check_post();
	require_once('actions/rejectiontest.action.php');
	rejectiontest_action();
	break;
    case 'finalizerejection':
	require_once('util/auth.util.php');
	$a = new ShiftAuth();
	$a->check_post();
	require_once('actions/finalizerejection.action.php');
	finalizerejection_action();
	break;
    case 'deleterejection':
	require_once('util/auth.util.php');
	$a = new ShiftAuth();
	$a->check_post();
	require_once('actions/deleterejection.action.php');
	deleterejection_action();
	break;
    case 'notifyrejection':
	require_once('util/auth.util.php');
	$a = new ShiftAuth();
	$a->check_post();
	require_once('actions/notifyrejection.action.php');
	notifyrejection_action();
	break;
    case 'forcesignup':
	require_once('util/auth.util.php');
	$a = new ShiftAuth();
	$a->check_post();
	require_once('actions/forcesignup.action.php');
	forcesignup_action();
	break;
    case 'forcesignupinsert':
	require_once('util/auth.util.php');
	$a = new ShiftAuth();
	$a->check_post();
	require_once('actions/forcesignupinsert.action.php');
	forcesignupinsert_action();
	break;
    case 'forcesignupclear':
	require_once('util/auth.util.php');
	$a = new ShiftAuth();
	$a->check_post();
	require_once('actions/forcesignupclear.action.php');
	forcesignupclear_action();
	break;
    case 'updateaccounting_reinit':
	require_once('util/auth.util.php');
	$a = new ShiftAuth();
	$a->check_post();
	require_once('actions/updateaccounting_reinit.action.php');
	updateaccounting_reinit_action();
	break;
    case 'swipesignup':
	require_once('util/auth.util.php');
	$a = new ShiftAuth();
	$a->check_post();
	require_once('actions/swipesignup.action.php');
	swipesignup_action();
	break;
    case 'missingid':
	require_once('util/auth.util.php');
	$a = new ShiftAuth();
	$a->check_post();
	require_once('actions/missingid.action.php');
	missingid_action();
	break;
    // error: action not recognized
    default:
	echo 'ERROR: page not found (do: '.$action.')';
	exit(0);
	break;
}


// output templated page
print $tpl->fetch();
