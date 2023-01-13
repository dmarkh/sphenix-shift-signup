<script>
function set_override(item) {
    document.cookie = item.name + "=" +escape( item.value ) + ";expires=0";
    alert(item.name + ' = ' + item.value + ' WAS ACTIVATED');
}

function flush_signup() {
    var flush = confirm('Do you REALLY want to delete all entries from ShiftSignup table? Are you sure that sPHENIX is not running now? Did you clear training slots using ForceSignup mode (REQUIRED)?');
    if (flush) {
	var onemore = confirm('Once again, are you 100% sure that you want to swipe all entries from ShiftSignup table and start new Run? All trainee slots are cleared with ForceSignup?');	
	if (onemore) {
	    $.post("index.php", { do: "swipesignup" }, function(data) { alert('Swipe performed: ' + data); } );
	} else {
	    alert('ShiftSignup Flush operation cancelled by user');
	}
    } else {
	alert('ShiftSignup Flush operation cancelled by user');
    }
}

</script>
<center>
<h2>NOTE: these options are absolete, please use <a href="?do=forcesignup">Force Signup</a> menu instead!</h2>
<h3>Config Override Menu</h3>
<p style="width: 60%; color: green;">Please change desired configuration parameters using select boxes below. Also, please read note at the bottom of the list.</p>
<table cellpadding="5" border="1" width="60%">
    <tr style="background-color: gold;"><th>SECTION</th><th>NAME</th><th>VALUE</th><th>DESCRIPTION</th></tr>
    <tr align="center">
	<td>generic</td>
	<td>suppress_emails</td>
	<td>
	    <select name="override_generic_suppress_emails" onChange="set_override(this)">
		<option value="" <?php if (empty($_COOKIE['override_generic_suppress_emails'])) { echo 'selected=selected'; }?>>NO</option>
		<option value="1" <?php if (!empty($_COOKIE['override_generic_suppress_emails'])) { echo 'selected=selected'; }?>>YES</option>
	    </select>
	</td>
	<td>Disable sending email notifications. DEFAULT: NO</td>
    </tr>
    <tr align="center">
	<td>generic</td>
	<td>override_subscription_limits</td>
	<td>
	    <select name="override_generic_override_subscription_limits" onChange="set_override(this)">
		<option value="" <?php if (empty($_COOKIE['override_generic_override_subscription_limits'])) { echo 'selected=selected'; }?>>NO</option>
		<option value="1" <?php if (!empty($_COOKIE['override_generic_override_subscription_limits'])) { echo 'selected=selected'; }?>>YES</option>
	    </select>
	</td>
	<td>Override subscription constrains like inability to unsubscribe during existing week. DEFAULT: NO</td>
    </tr>
    <tr align="center">
	<td>generic</td>
	<td>oversubscription_protection</td>
	<td>
	    <select name="override_generic_oversubscription_protection" onChange="set_override(this)">
		<option value="1" <?php if (!isset($_COOKIE['override_generic_oversubscription_protection'])) { echo 'selected=selected'; }?>>ENABLED</option>
		<option value="" <?php if (isset($_COOKIE['override_generic_oversubscription_protection'])) { echo 'selected=selected'; }?>>DISABLED</option>
	    </select>
	</td>
	<td>Check institutional limits (115%) when user subscribes to shifts. DEFAULT: ENABLED</td>
    </tr>
    <tr align="center">
	<td>generic</td>
	<td>email_maillist</td>
	<td>
	    <select name="override_generic_email_maillist" onChange="set_override(this)">
		<option value="shiftreport-hn@www.sphenix.bnl.gov" 
		    <?php if (!isset($_COOKIE['override_generic_email_maillist']) || $_COOKIE['override_generic_email_maillist'] == 'shiftreport-hn@www.sphenix.bnl.gov') { 
			echo 'selected=selected'; 
		    } ?>
		>ENABLED</option>
		<option value="arkhipkin@gmail.com" 
		    <?php if ($_COOKIE['override_generic_email_maillist'] == 'arkhipkin@gmail.com') { 
			echo 'selected=selected'; 
		    } ?>
		>DISABLED</option>
	    </select>
	</td>
	<td>Report unsubscribe events to this maillist. DEFAULT: ENABLED (report to shiftreport-hn@www.star.bnl.gov)</td>
    </tr>
    <tr>
	<td colspan="4" align="center">
	    <i><font color="green">ATTENTION: THOSE CHANGES AFFECT YOUR BROWSER ONLY, AND WILL BE AUTOMATICALLY CLEARED WHEN YOU CLOSE BROWSER. THERE IS NO NEED TO SET VALUES BACK TO DEFAULTS WHEN YOU ARE DONE.</font></i>
	</td>
    </tr>
</table>
<br><br>
<?php
$cfg =& Config::Instance();
$flush_button = $cfg->Get('run', 'display_warning_banner');  
if ($flush_button) {
?>
<h2><font color="red">AUTOMATIC SWEEP</font></h2>
<input type="button" name="clear_signup" value="FLUSH SIGNUP TABLE" onClick="flush_signup()">
<h2><font color="red">ATTENTION: USE ONCE *BEFORE* THE RUN! IRREVERSABLE OPERATION!</font></h2>
<h3><i>Trainee slots are not cleared for now because of the training record issues. Please use <a href="?do=forcesignup">ForceSignup mode</a> to remove trainees - it has proper check for that.</i></h3>
<?php } else { echo '<h1>display_warning_banner is set to NO in the configuration</h1>'; } ?>
