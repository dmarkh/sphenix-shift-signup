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

if (!function_exists('file_put_contents')) {
    function file_put_contents($filename, $data) {
        $f = @fopen($filename, 'w');
	if (!$f) {
	    return false;
	} else {
	    $bytes = fwrite($f, $data);
	    fclose($f);
	    return $bytes;
	}
    }
}

function get_shift_hours($id) {
    $id = intval($id);
    switch($id) {
	case 0:
	    return '0:00 - 8:00';
	    break;
	case 1:
	    return '8:00 - 16:00';
	    break;
	case 2:
	    return '16:00 - 0:00';
	    break;
	default:
	    return 'unknown';
	    break;
    }
}

function get_shift_type($id) {
    $id = intval($id);
    switch($id) {
	case 1:
	    return 'Period Coordinator';    
	break;
	case 2:
	    return 'Shift Leader';
	    break;
	case 3:
	case 4:
	    return 'Detector Operator';
	    break;
	case 5:
	    return 'Data Monitor Operator';
	    break;
	case 6:
	    return 'Shift Leader Trainee';
	    break;
	case 7: 
	    return 'Detector Operator Trainee';
	    break;
	case 8: 
	    return 'Offline QA';
	    break;
	default:
	    return 'Unknown';
	    break;    
    }
}

function get_country_code($country) {
    $country = trim(strtolower($country));
    switch ($country) {
	case 'us':
	case 'usa':
	case 'united states':
	    return 'us';
	    break;
	case 'uk':
	case 'united kingdom':
	    return 'uk';
	    break;
	case 'p. r. china':
	case 'china':
	    return 'ch';
	    break;
	case 'india':
	    return 'in';
	    break;
	case 'ru':
	case 'rus':
	case 'russia':
	    return 'ru';
	    break;
	case 'germany':
	    return 'gm';
	    break;
	case 'france':
	    return 'fr';
	    break;
	case 'the netherlands':
	case 'netherlands':
	    return 'nl';
	    break;
	case 'czech repulic':
	case 'czech republic':
	    return 'ez';
	    break;
	case 'brazil':
	    return 'br';
	    break;
	case 'croatia':
	    return 'hr';
	    break;
	case 'slovakia':
	    return 'lo';
	    break;
	case 'canada':
	case 'ca':
	    return 'ca';
	    break;
	case 'poland':
	    return 'pl';
	    break;
	case 'switzerland':
	    return 'sz';
	    break;
	case 'republic of korea':
	case 'korea':
	    return 'ks';
	    break;
	default:
	    return '';
	    break;
    }
}

function get_flag($country) {
    $country = trim(strtolower($country));
    switch ($country) {
	case 'us':
	case 'usa':
	case 'united states':
	    return 'us.png';
	    break;
	case 'uk':
	case 'united kingdom':
	    return 'uk.png';
	    break;
	case 'p. r. china':
	case 'china':
	    return 'ch.png';
	    break;
	case 'india':
	    return 'in.png';
	    break;
	case 'ru':
	case 'rus':
	case 'russia':
	    return 'ru.png';
	    break;
	case 'germany':
	    return 'ge.png';
	    break;
	case 'france':
	    return 'fr.png';
	    break;
	case 'the netherlands':
	case 'netherlands':
	    return 'ne.png';
	    break;
	case 'czech repulic':
	case 'czech republic':
	    return 'cz.png';
	    break;
	case 'brazil':
	    return 'br.png';
	    break;
	case 'croatia':
	    return 'cr.png';
	    break;
	case 'slovakia':
	    return 'sl.png';
	    break;
	case 'canada':
	case 'ca':
	    return 'ca.png';
	    break;
	case 'poland':
	    return 'po.png';
	    break;
	case 'switzerland':
	    return 'sw.png';
	    break;
	case 'republic of korea':
	case 'korea':
	    return 'ko.png';
	    break;
	default:
	    return '';
	    break;
    }
}

function get_ip_address() {
	$ip = 'unknown';
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
    $ip = $_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}

/*
function get_ip_address() {
    if ( isset($_SERVER["REMOTE_ADDR"]) && (strpos($_SERVER["REMOTE_ADDR"], '192.168') === false) ) {
	return $_SERVER["REMOTE_ADDR"];
    } else if ( isset($_SERVER["HTTP_X_FORWARDED_FOR"]) ) {
	return $_SERVER["HTTP_X_FORWARDED_FOR"];
    } else if ( isset($_SERVER["HTTP_CLIENT_IP"]) ) {
	return $_SERVER["HTTP_CLIENT_IP"];
    } 
    return 'unknown';
}
*/
