<?php

#include ('db.util.php');
#include ('config.util.php');

#$v = TrainReq::Instance();
#$res = $v->check_user('22607');
#echo 'ok';
#var_dump($res);

class TrainReq {

    var $destruct = 0;
    var $id_phonebook = 0;
    var $lname = '';

    function TrainReq() {                                                                                                                                                
        //destructor for php4 compatibility                                                                                                                    
        register_shutdown_function(array(&$this, '__destruct'));                                                                                               
    }                                                                                                                                                              
                                                                                                                                                                       
    function __destruct() {                                                                                                                                        
        if ($this->destruct > 0) return;                                                                                                                       
        $this->destruct = 1;                                                                                                                                   
	// do cleanup, if needed
    }

    static function &Instance () {                                                                                                                   
        static $instance;                                                                                                                                              
        if (!isset($instance)) {                                                                                                                              
            $c = __CLASS__;                                                                                                                                            
            $instance = new $c;                                                                                                                               
        }                                                                                                                                                              
        return $instance;                                                                                                                                     
    }

    function set_id_phonebook($id) {
	$this->id_phonebook = $id;
    }

    function set_lname($name) {
	$this->lname = $name;
    }

    function startsWith($haystack, $needle) { 
	return !strncmp($haystack, $needle, strlen($needle)); 
    }

    function endsWith($haystack, $needle) { 
	$length = strlen($needle); if ($length == 0) { return true; }; return (substr($haystack, -$length) === $needle); 
    }

    function request_itd_data($uid) {                                                                                                                                          
	$url = 'https://ias.bnl.gov/BNL.DataHub/Api/EmployeeWithClassesLookup?id='.$uid.'&latest=true';                                                                    
        $ch = curl_init();                                                                                                                                                 
        curl_setopt($ch, CURLOPT_URL, $url);                                                                                                                               
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);                                                                                                                    
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANYSAFE);                                                                                                              
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);                                                                                                                   
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);                                                                                                                    
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);                                                                                                                             
        $resp = curl_exec($ch);                                                                                                                                            
        curl_close($ch);                                                                                                                                                   
        $resp = json_decode($resp, true);                                                                                                                                  
        return $resp;                                                                                                                                                      
    } 

    function save_itd_data($data, $itd) {

	$req = array(                                                                                                                                                
            'GE-CYBERSEC'           => '0000-00-00 00:00:00',
            'TQ-GSO'                => '0000-00-00 00:00:00',
            'HP-V-001'              => '0000-00-00 00:00:00',
            'RC-SOCSTAR'            => '0000-00-00 00:00:00',
            'AD-CA_COLLIDER_USER'   => '0000-00-00 00:00:00',
            'AD-CA_COLLIDER_EXAM'   => '0000-00-00 00:00:00',
            'HP-IND-200'            => '0000-00-00 00:00:00',
            'AD-11.1'               => '0000-00-00 00:00:00',
            'AD-11.4.4'             => '0000-00-00 00:00:00',
            'AD-2.5.2'              => '0000-00-00 00:00:00',
            'AD-3.17'               => '0000-00-00 00:00:00'
        ); 

	if (!empty($data['GE-CYBERSEC'])) {
	    $req['GE-CYBERSEC'] = str_replace('T', ' ', $data['GE-CYBERSEC']['Expires']);
	}
	if (!empty($data['HP-V-001'])) {
	    $req['HP-V-001'] = '2050-01-01 00:00:00';
	}
	if (!empty($data['TQ-GSO'])) {
	    $req['TQ-GSO'] = '2050-01-01 00:00:00';
	}
	if (!empty($data['RC-SOCSTAR'])) {
	    $req['RC-SOCSTAR'] = str_replace('T', ' ', $data['RC-SOCSTAR']['Expires']);
	}
	if (!empty($data['AD-CA_COLLIDER_USER'])) {
	    $req['AD-CA_COLLIDER_USER'] = '2050-01-01 00:00:00';
	}
	if (!empty($data['AD-CA_COLLIDER_EXAM'])) {
	    $req['AD-CA_COLLIDER_EXAM'] = str_replace('T', ' ', $data['AD-CA_COLLIDER_EXAM']['Expires']);
	}
	if (!empty($data['HP-IND-200'])) {
	    $req['HP-IND-200'] = str_replace('T', ' ', $data['HP-IND-200']['Expires']);
	}
	if (!empty($data['AD-11.1'])) {
	    $req['AD-11.1'] = str_replace('T', ' ', $data['AD-11.1']['Expires']);
	}
	if (!empty($data['AD-11.4.4'])) {
	    $req['AD-11.4.4'] = str_replace('T', ' ', $data['AD-11.4.4']['Expires']);
	}
	if (!empty($data['AD-2.5.2'])) {
	    $req['AD-2.5.2'] = str_replace('T', ' ', $data['AD-2.5.2']['Expires']);
	}
	if (!empty($data['AD-3.17'])) {
	    $req['AD-3.17'] = str_replace('T', ' ', $data['AD-3.17']['Expires']);
	}

	$db  =& Db::Instance();
	if ( empty($itd['bnl_status']) ) { $itd['bnl_status'] = ''; };
	$query = 'INSERT INTO `ShiftSignup`.`ShiftReqs` (`id_phonebook`,`id_bnl`, `itd_name_first`, `itd_name_last`, `email`, `phone`, `id_bnl_status`, `tr_ge_cybersec_exp`, `tr_tq_gso_exp`, `tr_hp_v_001_exp`, `tr_rc_socstar_exp`, `tr_ad_ca_collider_user_exp`, `tr_ad_ca_collider_exam_exp`, `tr_hp_ind_200_exp`, `tr_ad_11_1_exp`, `tr_ad_11_4_4_exp`, `tr_ad_2_5_2_exp`, `tr_ad_3_17_exp` ) VALUES ('.intval($this->id_phonebook).', "'.$db->Escape($itd['id_bnl']).'", "'.$db->Escape($itd['name_first']).'", "'.$db->Escape($itd['name_last']).'", "'.$db->Escape($itd['email']).'", "'.$db->Escape($itd['phone']).'", "'.$db->Escape($itd['bnl_status']).'", "'.$req['GE-CYBERSEC'].'", "'.$req['TQ-GSO'].'", "'.$req['HP-V-001'].'", "'.$req['RC-SOCSTAR'].'", "'.$req['AD-CA_COLLIDER_USER'].'", "'.$req['AD-CA_COLLIDER_EXAM'].'", "'.$req['HP-IND-200'].'", "'.$req['AD-11.1'].'",  "'.$req['AD-11.4.4'].'",  "'.$req['AD-2.5.2'].'",  "'.$req['AD-3.17'].'") ON DUPLICATE KEY UPDATE `id_phonebook` = '.intval($this->id_phonebook).', `itd_name_first` = "'.$db->Escape($itd['name_first']).'", `itd_name_last` = "'.$db->Escape($itd['name_last']).'", `email` = "'.$db->Escape($itd['email']).'", `phone` = "'.$db->Escape($itd['phone']).'", `id_bnl_status` = "'.$db->Escape($itd['bnl_status']).'", `tr_ge_cybersec_exp` = "'.$req['GE-CYBERSEC'].'", `tr_tq_gso_exp` = "'.$req['TQ-GSO'].'", `tr_hp_v_001_exp` = "'.$req['HP-V-001'].'", `tr_rc_socstar_exp` = "'.$req['RC-SOCSTAR'].'", `tr_ad_ca_collider_user_exp` = "'.$req['AD-CA_COLLIDER_USER'].'", `tr_ad_ca_collider_exam_exp` = "'.$req['AD-CA_COLLIDER_EXAM'].'", `tr_hp_ind_200_exp` = "'.$req['HP-IND-200'].'", `tr_ad_11_1_exp` = "'.$req['AD-11.1'].'", `tr_ad_11_4_4_exp` = "'.$req['AD-11.4.4'].'", `tr_ad_2_5_2_exp` = "'.$req['AD-2.5.2'].'", `tr_ad_3_17_exp` = "'.$req['AD-3.17'].'";';
	$db->Query($query);
    }

    function load_itd_data($uid) {
	$uid = trim($uid);
	$db  =& Db::Instance();
	$query = 'SELECT * FROM `ShiftSignup`.`ShiftReqs` WHERE id_bnl = "'.$db->Escape($uid).'" LIMIT 1;';
	$res = $db->Query($query);
	if ( !empty($res) && count($res) == 1 && !empty($res[0]) ) { $res = $res[0]; }
	if ( !empty($res) ) {
            $trmap = array(
	    'GE-CYBERSEC'           => $res['tr_ge_cybersec_exp'], 
            'TQ-GSO'                => $res['tr_tq_gso_exp'],
	    'HP-V-001'              => $res['tr_hp_v_001_exp'],
            'RC-SOCSTAR'            => $res['tr_rc_socstar_exp'],
            'AD-CA_COLLIDER_USER'   => $res['tr_ad_ca_collider_user_exp'],
	    'AD-CA_COLLIDER_EXAM'   => $res['tr_ad_ca_collider_exam_exp'],
            'HP-IND-200'            => $res['tr_hp_ind_200_exp'],
            'AD-11.1'               => $res['tr_ad_11_1_exp'],
            'AD-11.4.4'             => $res['tr_ad_11_4_4_exp'],
            'AD-2.5.2'              => $res['tr_ad_2_5_2_exp'],
            'AD-3.17'               => $res['tr_ad_3_17_exp']
	    );
	    return $trmap;
	} 
	return array();
    }

    function check_user($uid) {
	$data = $this->request_itd_data($uid);
	//file_put_contents('/tmp/tribedy.txt', print_r($data, true));
	//print_r($data);

	if (empty($data)) return array('shifter' => false, 'leader' => false);

        $trmap = array(
	    'GE-CYBERSEC'           => array('desc' => 'Cyber Security Training',                           'link' => '', 'column' => 'tr_ge_cybersec_exp'),
            'TQ-GSO'                => array('desc' => 'Guest Site Orientation',                            'link' => '', 'column' => 'tr_tq_gso_exp'),
	    'HP-V-001'              => array('desc' => 'General Employee Training',                         'link' => '', 'column' => 'tr_hp_v_001_exp'),
            'RC-SOCSTAR'            => array('desc' => 'STAR Low Hazard Worker-Planned-Work for Users',     'link' => '', 'column' => 'tr_rc_socstar_exp'),
            'AD-CA_COLLIDER_USER'   => array('desc' => 'Collider User Trg. - Initial Classroom Training',   'link' => '', 'column' => 'tr_ad_ca_collider_user_exp'),
	    'AD-CA_COLLIDER_EXAM'   => array('desc' => 'Collider User Trg. - Annual Requal Challenge Exam', 'link' => '', 'column' => 'tr_ad_ca_collider_exam_exp'),
            'HP-IND-200'            => array('desc' => 'Hazard Communications',                             'link' => '', 'column' => 'tr_hp_ind_200_exp'),
            'AD-11.1'               => array('desc' => 'Conduct of Ops for RHIC Exp Shift Leaders',         'link' => '', 'column' => 'tr_ad_11_1_exp'),
            'AD-11.4.4'             => array('desc' => 'Procedure for Exciting the STAR Magnet',            'link' => '', 'column' => 'tr_ad_11_4_4_exp'),
            'AD-2.5.2'              => array('desc' => 'RHIC Operations Safety Limits/Accel Safety Env',    'link' => '', 'column' => 'tr_ad_2_5_2_exp'),
            'AD-3.17'               => array('desc' => 'Emrg Proc for STAR Detector & 1006 Complex',        'link' => '', 'column' => 'tr_ad_3_17_exp')
        );

        $shifter = array(
	    '0_AND' => array('GE-CYBERSEC','RC-SOCSTAR'),
	    '1_OR'  => array('TQ-GSO','HP-V-001'),
	    '2_OR'  => array('AD-CA_COLLIDER_USER','AD-CA_COLLIDER_EXAM')
	);

	$leader = array(
	    '0_AND' => array('GE-CYBERSEC','RC-SOCSTAR'),
    	    '1_OR'  => array('TQ-GSO','HP-V-001'),
	    '2_OR'  => array('AD-CA_COLLIDER_USER','AD-CA_COLLIDER_EXAM'),
	    '3_AND' => array('HP-IND-200', 'AD-11.1', 'AD-11.4.4', 'AD-2.5.2', 'AD-3.17')
	);

	$classes = $data['EmployeeClasses'];
        $reqclasses = array();

	// select known classes out of data array
        foreach($classes as $k => $v) {                                                                                                                                    
	    if (array_key_exists($v['CourseKey'], $trmap)) {
	        if ( isset( $reqclasses[$v['CourseKey']] ) && isset( $reqclasses[$v['CourseKey']]['Expires'] ) ) {
	    	    if ( strtotime( $reqclasses[$v['CourseKey']]['Expires'] ) 
	    		    < strtotime( $v['Expires'] ) ) {
	    		$reqclasses[$v['CourseKey']] = $v;
	    	    }
	    	} else {
	    	    $reqclasses[$v['CourseKey']] = $v;
		}
	        if (isset($reqclasses[$v['CourseKey']]['Equivalencies'])) {
		     unset($reqclasses[$v['CourseKey']]['Equivalencies']);
		}
	    }
	    // search for equivalents
	    if ( !empty($v['Equivalencies']) ) {
		foreach($v['Equivalencies'] as $k2 => $v2) {
		    if ( array_key_exists($v2['CourseKey'], $trmap) ) {
			if ( empty($reqclasses[$v2['CourseKey']]) || empty($reqclasses[$v2['CourseKey']]['Expires']) ) {
		    	    $reqclasses[$v2['CourseKey']] = $v;
	    		    $reqclasses[$v2['CourseKey']]['CourseKey'] = $v2['CourseKey'];
	    		    $reqclasses[$v2['CourseKey']]['CourseTitle'] = $v2['CourseEquivTitle'];
			    if (isset($reqclasses[$v2['CourseKey']]['Equivalencies'])) {
				 unset($reqclasses[$v2['CourseKey']]['Equivalencies']);
			    }
			} else if ( !empty($reqclasses[$v2['CourseKey']]) && !empty($reqclasses[$v2['CourseKey']]['Expires']) ) {
	    		    if ( strtotime( $reqclasses[$v2['CourseKey']]['Expires'] ) 
	    			< strtotime( $v['Expires'] ) ) {
	    			$reqclasses[$v2['CourseKey']] = $v;
	    			$reqclasses[$v2['CourseKey']]['CourseKey'] = $v2['CourseKey'];
	    			$reqclasses[$v2['CourseKey']]['CourseTitle'] = $v2['CourseEquivTitle'];
	    		    }
			}
		    }
		}
	    }
	}

	// if we are here, we need to save/update info:
	$itd_data = array(
	    'id_bnl' => $uid, 'name_first' => $data['EmployeeInfo']['FName'], 'name_last' => $data['EmployeeInfo']['Surname'],
	    'email' => $data['EmployeeInfo']['Email'], 'phone' => $data['EmployeeInfo']['TeleExt'], 'id_bnl_status' => $data['EmployeeInfo']['StatusKey']);
	if ( levenshtein(strtolower($data['EmployeeInfo']['Surname']), strtolower($this->lname) ) < 3 ) {
	    $this->save_itd_data( $reqclasses, $itd_data );
	}

	$shifter_bad = array();
        $shifter_valid = -1;
	foreach($shifter as $k => $v) {
	    if ($this->endsWith($k, 'AND')) {
	    	foreach($v as $k2 => $v2) {	
		    $state = false;
		    if (!array_key_exists($v2, $reqclasses)) { 
			$state = false;  
			$shifter_bad[$v2] = $trmap[$v2]; 
			$shifter_bad[$v2]['expires'] = '';
		    } 
		    else if  (empty($reqclasses[$v2]['Expires'])) { $state = true; } 
		    else if (strtotime($reqclasses[$v2]['Expires']) < time(0)) { 
			$state = false;  
			$shifter_bad[$v2] = $trmap[$v2]; 
			$shifter_bad[$v2]['expires'] = $reqclasses[$v2]['Expires'];
		    } 
		    else { $state = true; }
		    if ($shifter_valid === -1) { $shifter_valid = $state; }
		    else { $shifter_valid = $shifter_valid & $state; }
		}
	    } else if ($this->endsWith($k, 'OR')) {
		$status = -1;
		foreach ( $v as $k2 => $v2 ) {
		    $state = false;
		    if ( array_key_exists($v2, $reqclasses) && ( empty($reqclasses[$v2]['Expires']) || strtotime($reqclasses[$v2]['Expires']) > time(0) ) ) { $state = true; }
		    if ($status === -1) { $status = $state; }
		    else { $status = $status | $state; }
		}
		if ($shifter_valid === -1) { $shifter_valid = $status; }
		else { $shifter_valid = $shifter_valid & $status; }
                if ($status == false) {                                                                                                                                 
                    $key = implode(' OR ', $v);                                                                                                                         
                    foreach($v as $k2 => $v2) {
                        $shifter_bad[$key][] = $trmap[$v2];
			if (!empty($reqclasses[$v2])) {
			    $shifter_bad[$key]['expires'] = $reqclasses[$v2]['Expires'];
			} else {
			    $shifter_bad[$key]['expires'] = '';
			}
                    }                                                                                                                                                   
                } 
	    }
	}

	$leader_bad = array();
        $leader_valid = -1;
	foreach($leader as $k => $v) {
	    if ($this->endsWith($k, 'AND')) {
		foreach($v as $k2 => $v2) {	
		    $state = false;
		    if (!array_key_exists($v2, $reqclasses)) { 
			$state = false;  
			$leader_bad[$v2] = $trmap[$v2];
			$leader_bad[$v2]['expires'] = '';
		    } 
		    else if  (empty($reqclasses[$v2]['Expires'])) { $state = true; } 
		    else if (strtotime($reqclasses[$v2]['Expires']) < time(0)) { 
			$state = false;  
			$leader_bad[$v2] = $trmap[$v2]; 
			$leader_bad[$v2]['expires'] = $reqclasses[$v2]['Expires'];
		    } 
		    else { $state = true; }
		    if ($leader_valid === -1) { $leader_valid = $state; }
		    else { $leader_valid = $leader_valid & $state; }
		}
	    } else if ($this->endsWith($k, 'OR')) {
		$status = -1;
		foreach ( $v as $k2 => $v2 ) {
		    $state = false;
		    if ( array_key_exists($v2, $reqclasses) && ( empty($reqclasses[$v2]['Expires']) || strtotime($reqclasses[$v2]['Expires']) > time(0) ) ) { $state = true; }
		    if ($status === -1) { $status = $state; }
		    else { $status = $status | $state; }
		}
		if ($leader_valid === -1) { $leader_valid = $status; }
		else { $leader_valid = $leader_valid & $status; }
                if ($status == false) {                                                                                                                                 
                    $key = implode(' OR ', $v);                                                                                                                         
                    foreach($v as $k2 => $v2) {                                                                                                                         
                        $leader_bad[$key][] = $trmap[$v2];
			if (!empty($reqclasses[$v2])) {
			    $leader_bad[$key]['expires'] = $reqclasses[$v2]['Expires'];
			} else {
			    $leader_bad[$key]['expires'] = '';
			}
                    }                                                                                                                                                   
                } 
	    }
	}
        return array( 'shifter' => $shifter_valid, 'leader' => $leader_valid, 'itd' => $itd_data, 'shifter_bad' => $shifter_bad, 'leader_bad' => $leader_bad );
    }

} // class TrainReq
