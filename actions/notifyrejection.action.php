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

function redirect_to_main() {
    $host  = $_SERVER['HTTP_HOST'];
    $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $extra = 'index.php?do=rejection';
    header('Location: http://'.$host.$uri.'/'.$extra);
    exit;
}

function notifyrejection_action() {
    $cfg =& Config::Instance();
    $db  =& Db::Instance();

    
    $note = $db->Query('SELECT * FROM `ShiftSignup`.`ShiftRejection` WHERE 1 LIMIT 1');
    if (empty($note)) redirect_to_main();

    $par = array();
    $tpar = explode(' ', trim($note[0]['params'], ' \n\r\t'));
    foreach($tpar as $k => $v) {
	$tmp = explode(':', $v);
	$par[$tmp[0]] = intval($tmp[1]);
    }

    $dt = trim($note[0]['data'], ' \n\r\t|');
    $dt = explode('|', $dt);
    $inst = array();
    $ids = array();
    foreach($dt as $k => $v) {
	$tmp = explode(':', $v);
	$inst[$tmp[0]] = $tmp[1];
	$tmp2 = explode(',',$tmp[1]);
	foreach($tmp2 as $k2 => $v2) {
	    $ids[] = intval($v2);
	}
    }
    $phonebook_ids = $ids; // array of members to disable in main phonebook

    $ids = implode(',', $ids);

    $tmpdues = $db->Query('SELECT * FROM `ShiftSignup`.`ShiftAdmin` WHERE 1');
    $dues = array();
    foreach($tmpdues as $k => $v) {
	//$dues[$v['institution_id']]['required'] = $v['shifts_required'];
	$ttmp = explode(';',$v['historical_data']);
	list( $dues[$v['institution_id']]['required'], $dues[$v['institution_id']]['taken'] ) = explode(',', $ttmp[0]);
    }
    unset($tmpdues);
    $taken = $db->Query('SELECT institution_id, COUNT(*) AS tkn FROM `ShiftSignup`.Shifts WHERE personID > 0 GROUP BY institution_id');
    foreach($taken as $k => $v) {
	$dues[$v['institution_id']]['taken'] = $v['tkn'];
    }
    unset($taken);

    $inst = array();
    $m = $db->Query('SELECT Id, InstitutionId, FirstName, LastName, isAuthor, isExpert, Expertise, isEmeritus, UNIX_TIMESTAMP(NOW()) as tsnow, UNIX_TIMESTAMP(LeaveDate) as tsld, (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(LeaveDate)) as tsdiff FROM `starweb`.members WHERE Id IN ('.$ids.') ORDER BY LastName ASC');
    if (empty($m)) { redirect_to_main(); };

    foreach($m as $k => $v) {
	$inst[$v['InstitutionId']]['authors'][] = $v;
    }
    $reps = array();
    foreach($inst as $k => $v) {
	$sql = 'SELECT i.InstitutionName as name, i.CouncilRepId, m.FirstName as fn, m.LastName as ln, m.EmailAddress as email FROM `starweb`.`institutions` i, `starweb`.`members` m WHERE i.Id = '.intval($k).' AND m.Id = i.CouncilRepId LIMIT 1;';
	$rep = $db->Query($sql);
	$inst[$k]['repname'] = $rep[0]['fn'].' '.$rep[0]['ln'];
	$inst[$k]['repmail'] = $rep[0]['email'];
	$inst[$k]['name'] = $rep[0]['name'];
    }

    $phdb  =& Db::Instance("phonebook_database");
    
    foreach($inst as $k => $v) {
	//$to = 'arkhipkin@bnl.gov, jlauret@bnl.gov';
	//$to = 'arkhipkin@bnl.gov';
	$to = $v['repmail'].', starcoun-l@lists.bnl.gov';
	$subj = 'Urgent ShiftSignup Notice. Authors to be rejected - '.$v['name'];
	$body = 'Dear '.$v['repname']." (".$v['repmail']."), \n\n\n";
	$rauth = array();
	$XX = $dues[$k]['required'];
	$YY = $dues[$k]['taken'];
	$mperc = round( ( $XX - $YY ) / $XX * 100.0, 0);

	foreach($v['authors'] as $k2 => $v2) {
	    $rauth[] = $v2['FirstName'].' '.$v2['LastName'];
	    
	    // SQL update goes here for each rejected person
	    $dreason = 'AUTHORSHIP will be OFF for a year. This author was disabled on '.date('r').' due to his/her institution not fulfilling shift obligations: '.$mperc.'% shift missing';
	    $q = 'UPDATE `starweb`.`members` SET IsAuthor = "N", DisabledDate = NOW(), DisableReason = "'.$phdb->Escape($dreason).'" WHERE Id = '.$v2['Id'];
	    $phdb->Query($q);

	    //print_r($q);
	    //echo "\n";
	}

	//." Our record further indicates that your institution missed their shift obligations "
	//.$par['T']." times or more in ".$par['P']." years at a level of ".$par['M']."% or greater."

	$body .= "This Email is to inform you of a possible author reduction for your institution.\n"
	    ."Executive summary:\n"
	    ." Your institution, ".$v['name'].", repeatedly missed fulfilling their shift obligations and duties.\n"
	    ." To compensate, the following authors have been flagged for removal from your institution author list: "
	    .implode(', ', $rauth)
	    ."\n\n"
	    ."Explanation & procedure:\n"
	    ." You were assigned ".$XX." shifts and covered for ".$YY." only, creating a shift coverage deficit "
	    ."for this year of "
	    . ( $XX - $YY ) ." (".round( ( $XX - $YY ) / $XX * 100.0, 0)." %)."

	    ." Our records indicates that your institutions missed their "
	    ."shift duties at levels greater or equal to ".$par['M']." averaging"
	    ."over ".$par['P']." years (and accounting for excess shifts taken"
	    ."in any years in this period as a bonus for other years)."

	    ." According to STAR policy, your author list is scheduled for a reduction in force by "
	    .count($rauth)." authors.\n"
	    ."\n\n"
	     ."If your institution is eligible for reduction because of shifts missed during a run extension, "
	     ."please indicate it to us and these shift may be transferred to the next running period without "
             ."penalty.\n"

	    ."Shall you have any other explaination or claims, please, follow the \"Appeals\" procedures "
	     ."described in PSN0545 (under section 8).\n" 
	  
	    ." We have made an initial random selection of authors for your consideration."
	    ." Within two weeks, please let us know if you wish to swap any of those names toward another author"
	    ." providing a 1 to 1 name replacement.\n"
	    ."If we do not hear from you within two weeks, this list will become effective and those flagged "
	    ."individual(s) will then be losing their authorship privileges for at least a year, subject to "
	    ."the regain of author rule from PSN0545.\n\n"

	    ."\n\n"
	    ."This is an automated message." 
	    ."You may answer this Email with the author swap information. "
	    ."Any other communication should go through STAR management.\n\n\n" 
	    ."Thank you for your attention,\nShiftSignup Accounting Module"; 
 
	$headers = 'From: root@www.sphenix.bnl.gov' . "\r\n" .
	    'Reply-To: arkhipkin@bnl.gov' . "\r\n" .
	    'Cc: arkhipkin@bnl.gov' . "\r\n" .
	    'X-Mailer: PHP/' . phpversion();
	    mail($to,$subj,$body, $headers);

    }
    $fin = $db->Query('UPDATE `ShiftSignup`.`ShiftRejection` SET isSent = 1 WHERE 1');

    // test only, do not uncomment :)
    //$res = $phdb->Query('UPDATE `starweb`.`members` SET IsAuthor = "Y", DisabledDate = NOW(), DisableReason = "Shift Signup Automatic Rejection action" WHERE Id IN (147)');
    //print_r($res);
    //exit;

    redirect_to_main();    
    exit;        
}


