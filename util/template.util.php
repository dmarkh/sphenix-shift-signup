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

class Template {
    var $vars = [];
	var $path = '';
	var $file = '';
    function __construct() { 
		$path = dirname(__FILE__);
		$path = str_replace(basename($path), '', $path);
		$path .= 'templates/';
		$this->path = $path; 
		$this->vars = array(); 
    }
    function set_path($path) { $this->path = $path; }
    function set($name, $value) { $this->vars[$name] = $value; }
    function append($name, $value) { if (isset($this->vars[$name])) { $this->vars[$name] .= $value; } else { $this->vars[$name] = $value; }	}
    function set_vars($vars, $clear = false) {
	if($clear) { $this->vars = $vars; } else { if(is_array($vars)) $this->vars = array_merge($this->vars, $vars); }
    }
    function set_file($file) { $this->file = $file; }
    function fetch() {
		extract($this->vars); ob_start(); include($this->path . $this->file); $contents = ob_get_contents(); ob_end_clean(); return $contents;
    }
    static function &Instance() {
	static $instance;
        if (!isset($instance)) {
            $c = __CLASS__;
            $instance = new $c;
        }
        return $instance;
    }
}

