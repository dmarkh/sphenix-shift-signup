<?php

class ShiftAuth {
    var $user_id;
    var $username;
    var $password;
    var $ok;
    var $salt = '43$PHENX34';
    var $domain = 'www.sphenix.bnl.gov';

	function __construct() {
		$this->ShiftAuth();
	}

    function ShiftAuth() {
      $cfg =& Config::Instance();
      $this->rlogin = $cfg->Get('access', 'protected_login');
      $this->rpass  = $cfg->Get('access', 'protected_password');
      $this->user_id = 0;
      $this->username = "Guest";
      $this->ok = false;
    }

    function Init() {
      if ( !$this->check_session() ) { return $this->check_cookie(); };
      return $this->ok;
    }

    function check_post() {
	if ( !empty($_POST['do']) && !empty($_POST['username']) && !empty($_POST['password']) && !empty($_POST['LOGIN']) ) {
	    if ( $this->login($_POST['username'], $_POST['password']) ) {
		return true;
	    }
	    $this->display_login_page('Incorrect login/password, please try again.');
	    exit(0);
	}
	if ( !$this->Init() ) {
	    $this->display_login_page();
	    exit(0);
	}
    }

    function check_session() {
      if(!empty($_SESSION['shift_auth_username']) && !empty($_SESSION['shift_auth_password'])) {
        return $this->check($_SESSION['shift_auth_username'], $_SESSION['shift_auth_password']);
      } else {
        return false;
      }
    }

    function check_cookie() {
        if(!empty($_COOKIE['shift_auth_username']) && !empty($_COOKIE['shift_auth_password'])) {
    	  return $this->check($_COOKIE['shift_auth_username'], $_COOKIE['shift_auth_password']);
	} else {
    	  return false;
        }
    }

    function login($username, $password) {
        global $db;
	if ($username != $this->rlogin || $password != base64_decode($this->rpass)) { return false; }
        $this->user_id = 1;
        $this->username = $username;
        $this->ok = true;
        $_SESSION['shift_auth_username'] = $username;
        $_SESSION['shift_auth_password'] = md5($password . $this->salt);
        setcookie("shift_auth_username", $username, 0, "/", $this->domain);
        setcookie("shift_auth_password", md5($password . $this->salt), 0, "/", $this->domain);
        return true;
    }

    function check($username, $password) {
      global $db;
      if ($username != $this->rlogin) return false;
      if (md5(base64_decode($this->rpass) . $this->salt) != $password) return false;
      $this->user_id = 1;
      $this->username = $username;
      $this->ok = true;
      return true;
    }

    function logout() {
      $this->user_id = 0;
      $this->username = "Guest";
      $this->ok = false;
      $_SESSION['shift_auth_username'] = "";
      $_SESSION['shift_auth_password'] = "";
      setcookie("shift_auth_username", "", time() - 3600, "/", $this->domain);
      setcookie("shift_auth_password", "", time() - 3600, "/", $this->domain);
    }

    function display_login_page($message = '') {
    global $action;
    echo '	
    <HTML> 
    <HEAD> 
    <TITLE>LOGIN PAGE</TITLE> 
    <BODY> 
    <center>    
    <form method="post" action="" class="login_form">
    <table class="login_table" style="border: 1px solid black; padding: 5px; margin-top: 50px;">
    <th><img src="img/icons/keys.png" border="0" alt="protected"></th><th align="left" colspan="2">SPHENIX SHIFT SIGN-UP PROTECTED AREA</th>
    <tr><td>&nbsp;</td><td>LOGIN NAME</td><td><input type="text" name="username"></td></tr>
    <tr><td>&nbsp;</td><td>PASSWORD</td><td><input type="password" name="password"></td></tr>
    <tr><td>&nbsp;</td><td colspan="2" align="center"><input type="submit" name="LOGIN" value="LOGIN" style="margin-top: 10px; margin-bottom: 10px;"></td></tr>
    <tr><td></td><td colspan="2" style="color: red; text-align: center;">'.$message.'</td></tr>
    </table>
    <input type="hidden" name="do" value="process_auth">
    <input type="hidden" name="former_do" value="'.$action.'">
    </form>
    </BODY>
    </HTML>
    ';

    }

}