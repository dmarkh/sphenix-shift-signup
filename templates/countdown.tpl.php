<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
<title>sPHENIX ShiftSignup countdown</title>
<style type="text/css">
@import "js/countdown/jquery.countdown.css";
#defaultCountdown { width: 240px; height: 45px; }
</style>
<script type="text/javascript" src="js/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="js/countdown/jquery.countdown.pack.js"></script>
<script type="text/javascript">
function liftOff() { 
    window.location.reload();
}
$(function () {
    $('#defaultCountdown').countdown({until: +<?=$time_diff ?>, onExpiry: liftOff});
});
</script>
</head>
<body>

<center>
<h1 style="margin-top: 150px; color: green;">sPHENIX Shift Sign-up for <?php echo Config::Instance()->Get('run', 'name'); ?> will start in about :</h1>
<div id="defaultCountdown"></div>
<h1 style="color: green;">This page will update itself automatically.</h1>
<br><br>
<?php if (!empty($preset_name)) { ?>
<h3>ShiftSignup will automatically select <span style="color: green;"><?=$preset_name ?></span> upon opening.<br> Thank you for checking your training records.</h3>
<?php } ?>
<br><br>
<h3><a href="/ShiftSignupRun21/" style="color: silver; text-decoration: none;"><font color="red">OLD</font> ShiftSignup link for Run 21</a></h3>

<?php 
 require('footer.tpl.php');
?>