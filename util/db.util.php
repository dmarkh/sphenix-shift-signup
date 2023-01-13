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

class Db {

	var $db;
	var $destruct = 0;
	var $error = false;
	var $error_message = '';

	function Db() {
		//destructor for php4 compatibility
		register_shutdown_function(array(&$this, '__destruct'));
	}

	function __destruct() {
		if ($this->destruct > 0) return;
		$this->destruct = 1;
		$this->Close();
	}

    static function &Instance ($domain = 'main_database') {
		//$domain = 'main_database';
		$cfg = Config::Instance();
		if ($domain == 'main_database' && $cfg->Get('run', 'enable_fake_database')) {
	    	$domain = 'fake_database';
		}

		static $instance;
        if (!isset($instance[$domain])) {
            $c = __CLASS__;
            $instance[$domain] = new $c;
			$instance[$domain]->InitFromConfig($domain);
        }
        return $instance[$domain];
    }

	function Init($dsn, $user, $pass) {
		$this->db = mysqli_connect($dsn, $user, $pass);
		if ( !$this->db ) {
    		    echo 'Could not connect to the database';
		}
		//mysql_set_charset('utf8',$this->db);
		//mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'", $this->db);
		//mysql_query('SET NAMES "utf8" COLLATE "utf8_general_ci"', $this->db );
	}

	function InitFromConfig($cfg_name) {
		$cfg = Config::Instance();
		$this->Init($cfg->Get($cfg_name,'host').':'.$cfg->Get($cfg_name,'port'),
					$cfg->Get($cfg_name,'user'),
					$cfg->Get($cfg_name,'pass'));  
	}

	function Query($sql) {
		$result = mysqli_query($this->db, $sql);
		$this->error_message = mysqli_error( $this->db );
		$rows = array();
		if (!$result) { 
		    $this->error = true;
		    return false;
		} else { 
		    $this->error = false;
		    if ( is_object($result) && mysqli_num_rows($result) != 0 ) {
    			while ($row = mysqli_fetch_assoc($result)) {                                                                                                                      
			    if (is_array($row) && count($row) == 1) {
				$keys = array_keys($row);
				$rows[$keys[0]][] = $row[$keys[0]];
			    } else {
			    	$rows[] = $row;
			    }
			} 
		    }
		}
		return $rows; 
	}

	function IsError() {
	    return $this->error;
	}

	function GetErrorMessage() {
	    return $this->error_message;
	}

	function GetMySQL() {
		if (isset($this->db)) {
			return $this->db;
		}
		return NULL;
	}

	function Close() {
		if ( !empty($this->db) ) {
			mysqli_close($this->db);
			unset($this->db);
		}
	}
	function Escape($query) {
	    if (isset($this->db)) {
			return mysqli_real_escape_string($this->db, $query);
	    }
	    return NULL;
	}
}

