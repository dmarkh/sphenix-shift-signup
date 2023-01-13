<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
<title>sPHENIX ShiftSignup Training Check</title>
<style type="text/css">
@import "js/countdown/jquery.countdown.css";
#defaultCountdown { width: 240px; height: 45px; }
</style>
<script type="text/javascript" src="js/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="js/jquery.cookie.js"></script>
<script>
$(document).ready(function(){
    $('#decline').click(function() {
	$.cookie('labid', '0');
	window.location.href = "index.php";
    });
    $('#accept').click(function() {
	window.location.href = "index.php?do=bnlcheck_display";
    });
});
</script>
</head>
<body>

<center>
<h1>Do you want to check your sPHENIX-BNL training records?</h1>
<h3>If you accept this check, you will be allowed to pre-select your name during ShiftSignup count-down</h3>

<input type="button" id="accept" name="accept" value="ACCEPT"> :: <input type="button" id="decline" name="decline" value="DECLINE">

<?php 
 require('footer.tpl.php');
?>