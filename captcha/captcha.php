<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PHP math captcha</title>
<link rel="stylesheet" type="text/css" href="captcha/stylesheet.css" />
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
    <h1>SPHENIX SHIFT SIGNUP</h1>
    <h2>Please type three numbers you see on the image into the box below<br><i>(no spaces, like '135')</i></h2>
    <form method="post" action="">
        <img align="middle" src="captcha/image.php" alt="Click to reload image" title="Click to reload image" id="captcha" onclick="javascript:reloadCaptcha()" /> = 
        <input type="text" name="secure" value="what's the number?" onclick="this.value=''" />
        <input type="submit" value="Submit" /><br />
        <span class="explain">click on the image to reload it</span>
    </form>
	<?php echo $error; ?>
    <h1>ANTI-BOT PROTECTION</h1>
    <h3><i>you are required to pass this check only once per browser session</i></h3>
</div>    
</body>
</html>
