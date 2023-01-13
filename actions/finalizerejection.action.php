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

function finalizerejection_action() {
    $err = '';
    $warn = '';
    $tpl =& Template::Instance();    
    $cfg =& Config::Instance();
    $db  =& Db::Instance();

    $par = array();
    $par['I'] = $_GET['par_I'] ? $_GET['par_I'] : 5;
//    $par['T'] = $_GET['par_T'] ? $_GET['par_T'] : 3;
    $par['P'] = $_GET['par_P'] ? $_GET['par_P'] : 5;
    $par['M'] = $_GET['par_M'] ? $_GET['par_M'] : 33;


    if (empty($_GET['ids'])) {
	$err = 'ERROR: No user IDs were provided';
    } else {
	$tmpdues = $db->Query('SELECT * FROM `ShiftSignup`.`ShiftAdmin` WHERE 1');
	$dues = array();
	foreach($tmpdues as $k => $v) {
//	    $ttmp = explode($v['historical_data']);
//	    $dues[$v['institution_id']]['required'] = 
//	    list($dues[$v['institution_id']]['required'],$dues[$v['institution_id']]['taken']) = explode(',', $ttmp[0]);
	}
	unset($tmpdues);

	$taken = $db->Query('SELECT institution_id, COUNT(*) AS tkn FROM `ShiftSignup`.Shifts WHERE personID > 0 GROUP BY institution_id');
	foreach($taken as $k => $v) {
	    $dues[$v['institution_id']]['taken'] = $v['tkn'];
	}
	unset($taken);

	$inst = array();
	$m = $db->Query('SELECT Id, InstitutionId, FirstName, LastName, isAuthor, isExpert, Expertise, isEmeritus, UNIX_TIMESTAMP(NOW()) as tsnow, UNIX_TIMESTAMP(LeaveDate) as tsld, (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(LeaveDate)) as tsdiff FROM `starweb`.members WHERE Id IN ('.$_GET['ids'].') ORDER BY LastName ASC');
	if (!empty($m) && is_array($m)) {
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

	    $storage_text = '<pre>';
	    $i = 0;
	    $storage_data = '';
	    $rids = array();
	    foreach($inst as $k => $v) {
		$i++;
		$storage_text .= $i.') Institution: '.$v['name'].', Representative: '.$v['repname'].' ('.$v['repmail'].')'."\n";
		$storage_text .= '  Rejected Authors: ';
		$rauth = array();
		foreach ($v['authors'] as $k2 => $v2) {
		    $rauth[] = $v2['FirstName'].' '.$v2['LastName'];		    
		    $rids[$k][] = $v2['Id'];
		}
		$storage_text .= implode(', ', $rauth)."\n";
	    }
	    $storage_text .= '<pre>';
	    foreach($rids as $k => $v) {
		$storage_data .= '|'.$k.':'.implode(',',$v);
	    }

	    //$params = 'I:'.$par['I'].' T:'.$par['T'].' P:'.$par['P'].' M:'.$par['M'];
	    $params = 'I:'.$par['I'].' P:'.$par['P'].' M:'.$par['M'];
		$db->Query('INSERT INTO `ShiftSignup`.`ShiftRejection` ( authors, ip, data, params ) VALUES ("'.$db->Escape($storage_text).
		'", "'.$db->Escape(get_ip_address()).'", "'.$db->Escape($storage_data).'", "'.$db->Escape($params).'")');
    
	    foreach($inst as $k => $v) {
		$to = 'arkhipkin@bnl.gov, jlauret@bnl.gov';
		//$to = 'arkhipkin@bnl.gov';
		// $to = $v['repmail'].', jlauret@bnl.gov, arkhipkin@bnl.gov';
		$subj = 'Urgent ShiftSignup Notice. Authors to be rejected - '.$v['name'];
		$body = 'Dear '.$v['repname']." (".$v['repmail']."), \n\n\n";
		$rauth = array();
		foreach($v['authors'] as $k2 => $v2) {
		    $rauth[] = $v2['FirstName'].' '.$v2['LastName'];
		}

		$XX = $dues[$k]['required'];
		$YY = $dues[$k]['taken'];
		$body .= "This Email is to inform you of an author reduction for your institution.\n"
		    ."Executive summary:\n"
		    ." Your institution, ".$v['name'].", repeatedly missed fulfilling their shift obligations and duties.\n"
		    ." To compensate, the following authors have been flagged for removal from your institution author list: "
		    .implode(', ', $rauth)
		    ."\n\n"
		    ."Explanation & procedure:\n"
		    ." You were assigned ".$XX." shifts and covered for ".$YY." only, creating a shift coverage deficit for this year of "
		    . ( $XX - $YY ) ." (".round( ( $XX - $YY ) / $XX * 100.0, 0)." %). Our record further indicates that your institution missed their shift obligations more than "
		    .$par['T']." times in ".$par['P']." years at a level of ".$par['M']."% or greater. According to STAR policy, your author list is scheduled for a reduction in force by "
		    .count($rauth)." authors.\n"
		    ."\n\n"
		    ." We have made an initial random selection of authors for your consideration. Within two weeks, please let us know if you  wish to swap any of those names toward another author. If we do not hear from you within two weeks, this list will become effective and those flagged individual will then be losing their author privileges for at least a year, subject to the regain of author rule from PSN0545. Shall you have any claim,"
		    ." please, follow the \"Appeals\" procedures described in PSN0545 (under section 8).\n"
		    //." please follow the procedure section 8 of PSN0545 (\"Appeals\").\n"
		    ."\n\n"
		    ."This is an automated message."
		    ."You may answer with the account of author swap. Any other communication should go through STAR management.\n\n\n"
		    ."Thank you for your attention,\nShiftSignup Accounting Module";

		$headers = 'From: starshift@www.star.bnl.gov' . "\r\n" .
		    'Reply-To: starshift@www.star.bnl.gov' . "\r\n" .
		    'X-Mailer: PHP/' . phpversion();
		//mail($to,$subj,$body, $headers);
	    }
		
	}
    }

    $host  = $_SERVER['HTTP_HOST'];
    $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $extra = 'index.php?do=rejection';
    header("Location: http://$host$uri/$extra");
    exit;

}


