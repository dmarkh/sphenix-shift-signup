<?php
	/**
	 * PHP MATH CAPTCHA
	 * Copyright (C) 2010  Constantin Boiangiu  (http://www.php-help.ro)
	 * 
	 * This program is free software: you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation, either version 3 of the License, or
	 * (at your option) any later version.
	 * 
	 * This program is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 * 
	 * You should have received a copy of the GNU General Public License
	 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 **/
	
	/**
	 * @author Constantin Boiangiu
	 * @link http://www.php-help.ro
	 * 
	 * This script is provided as-is, with no guarantees.
	 */
	
	/* the captcha result is stored in session */
	session_start();
	$session_id = session_id();
	session_regenerate_id();

	/* a simple form check */
	if( isset( $_POST['secure'] ) )
	{
		if(md5(sha1($_POST['secure'].$session_id,false),false) != $_SESSION['security_number'])
		{
			$error = '<font color="red">Incorrect, please try again...</font>';
		}
		else
		{
			$error = '<font color="green">Excellent! You seem to be a human, not some malicious script.</font>';
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PHP math captcha</title>
<link rel="stylesheet" type="text/css" href="stylesheet.css" />
<script language="javascript" type="text/javascript">
	/* this is just a simple reload; you can safely remove it; remember to remove it from the image too */
	function reloadCaptcha()
	{
		document.getElementById('captcha').src = document.getElementById('captcha').src+ '?' +new Date();
	}
</script>
</head>

<body>
<div id="container" style="text-align: center; margin-top: 100px;">
    <h1>ANTI-BOT CHECK</h1>
    <h2>Please type three numbers you see on the image into the box below<br><i>(no spaces, like '135')</i></h2>
    <form method="post" action="">
        <img align="middle" src="image.php" alt="Click to reload image" title="Click to reload image" id="captcha" onclick="javascript:reloadCaptcha()" /> = 
        <input type="text" name="secure" value="what's the number?" onclick="this.value=''" />
        <input type="submit" value="am I right?" /><br />
        <span class="explain">click on the image to reload it</span>
    </form>
	<?php echo $error; ?>
</div>    
</body>
</html>
