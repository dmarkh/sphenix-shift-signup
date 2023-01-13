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

class Config {
	
    var $cfg = NULL;
    var $destruct = 0;                                                                                                                                       

	function __construct() {
		$this->Config();
	}
                                                                                                                                                             
    function Config() {                                                                                                                                       
		$this->Init();
    }                                                                                                                                                        
                                                                                                                                                             
    static function &Instance() {                                                                                                                            
        static $instance;                                                                                                                                    
        if (!isset($instance)) {                                                                                                                    
            $c = __CLASS__;                                                                                                                                  
            $instance = new $c;                                                                                                                     
        }                                                                                                                                                    
        return $instance;                                                                                                                           
    }       

    function Init() {
	$path = dirname(__FILE__);
	$path = str_replace(basename($path), '', $path);
	$path .= 'config/config.ini';

	$this->cfg = parse_ini_file($path, true);
    
	// decrypt some values
	//$this->cfg['main_database']['host'] = config_decrypt( $this->cfg['main_database']['host'] );
	//$this->cfg['main_database']['port'] = intval(config_decrypt( $this->cfg['main_database']['port'] ));
	//$this->cfg['main_database']['user'] = config_decrypt( $this->cfg['main_database']['user'] );
	//$this->cfg['main_database']['pass'] = config_decrypt( $this->cfg['main_database']['pass'] );

	//$this->cfg['phonebook_database']['host'] = config_decrypt( $this->cfg['phonebook_database']['host'] );
	//$this->cfg['phonebook_database']['port'] = intval(config_decrypt( $this->cfg['phonebook_database']['port'] ));
	//$this->cfg['phonebook_database']['user'] = config_decrypt( $this->cfg['phonebook_database']['user'] );
	//$this->cfg['phonebook_database']['pass'] = config_decrypt( $this->cfg['phonebook_database']['pass'] );

	//$this->cfg['fake_database']['host'] = config_decrypt( $this->cfg['fake_database']['host'] );
	//$this->cfg['fake_database']['port'] = intval(config_decrypt( $this->cfg['fake_database']['port'] ));
	//$this->cfg['fake_database']['user'] = config_decrypt( $this->cfg['fake_database']['user'] );
	//$this->cfg['fake_database']['pass'] = config_decrypt( $this->cfg['fake_database']['pass'] );

    }	

    function Get($section, $param = NULL) {
	if (!empty($param)) {
	    if (!empty($_COOKIE['override_'.$section.'_'.$param])) {
		return $_COOKIE['override_'.$section.'_'.$param];
	    }
	    return $this->cfg[$section][$param];
	}
	if (!empty($_COOKIE['override_'.$section])) {
	    return $_COOKIE['override_'.$section];
	}
	return $this->cfg[$section];
    }

    function ListAll() {
	echo '<pre>';
	print_r($this->cfg);
	echo '</pre>';
    }

}

function config_encrypt($str){
  $key = "A careful analysis of the process of observation in atomic physics has shown that the subatomic particles have no meaning as isolated entities, but can only be understood as interconnections between the preparation of an experiment and the subsequent measurement.";
  for($i=0; $i<strlen($str); $i++) {
     $char = substr($str, $i, 1);
     $keychar = substr($key, ($i % strlen($key))-1, 1);
     $char = chr(ord($char)+ord($keychar));
     $result.=$char;
  }
  return urlencode(base64_encode($result));
}


function config_decrypt($str){
  $str = base64_decode(urldecode($str));
  $result = '';
  $key = "A careful analysis of the process of observation in atomic physics has shown that the subatomic particles have no meaning as isolated entities, but can only be understood as interconnections between the preparation of an experiment and the subsequent measurement.";
  for($i=0; $i<strlen($str); $i++) {
    $char = substr($str, $i, 1);
    $keychar = substr($key, ($i % strlen($key))-1, 1);
    $char = chr(ord($char)-ord($keychar));
    $result.=$char;
  }
return $result;
}
