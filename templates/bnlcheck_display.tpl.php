<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
<title>sPHENIX ShiftSignup Training Check</title>
<style type="text/css">
@import "js/countdown/jquery.countdown.css";
#defaultCountdown { width: 240px; height: 45px; }
</style>
<link href="css/styles.css" type="text/css" rel="stylesheet"> 
<script type="text/javascript" src="js/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="js/jquery.cookie.js"></script>
<script type="text/javascript" src="js/countdown/jquery.countdown.pack.js"></script>                                                                                   
<script language="javascript" src="js/jquery.chained.min.js"></script>
<script>
$(document).ready(function(){
    $("#person").chained("#inst"); 
    $('#check_id').click(function() {
	var inst = $('#inst').val();                                                                                                                                       
	var pers = $('#person').val();                                                                                                                                     
        var labid = $('#bnl_id').val();
	$.getJSON('index.php?do=bnlcheck_check&inst='+inst+'&pers='+pers+'&labid='+labid, function( data, status, xhr ) {
	    if (data.error == undefined && data.itd ) {
		var shifter = '<span style="color: red">NO</span>';
		var leader = '<span style="color: red">NO</span>';
		$('#message').css({color: '#090'}).text('SUCCESS: record for '+data.itd.name_first+' '+data.itd.name_last+' retrieved and checked');
		if (data.shifter == 1) {
		    shifter = '<span style="color: green; font-weight: bold;">YES</span>';
		}
		if (data.leader == 1) {
		    leader = '<span style="color: green; font-weight: bold;">YES</span>';
		}
		$('#message').append('<br>Regular Shifter trainings: '+shifter+', Leader trainings: '+leader);
		var message = '';
		for (var i in data.shifter_bad) {
		    if (i.search(' OR ') != -1) {
			var mess = [];
			for (var j in data.shifter_bad[i]) {
			    mess.push(j + ' : '+data.shifter_bad[i][j].desc);
			}
			message += mess.join(' OR ') + '<br>';
		    } else {
			message += i+' : '+data.shifter_bad[i].desc + '<br>';
		    }
	    	}
		if (message.length > 0) { $('#message').append('<br><br><b>Expired or missing Shifter trainings:</b><br><span style="color: red; text-align: left;"> '+message+'</span><br><br>Please use the following link to find out what those trainings are: <a href="https://www.bnl.gov/guv/training/RHIC/STAR/" target="_blank">STAR ShiftTaker Trainings</a>'); }
		message = '';
		for (var i in data.leader_bad) {
		    if (i.search(' OR ') != -1) {
			var mess = [];
			for (var j in data.leader_bad[i]) {
			    mess.push(j + ' : '+data.leader_bad[i][j].desc);
			}
			message += mess.join(' OR ') + '<br>';
		    } else {
			message += i+' : '+data.leader_bad[i].desc + '<br>';
		    }
	    	}
		if (message.length > 0) { $('#message').append('<br><br><b>Expired or missing Leader trainings:</b><br><span style="color: red; text-align: left;">'+message+'</span><br><br>Please use the following link to find out what those trainings are: <a href="https://www.bnl.gov/guv/training/RHIC/STAR/" target="_blank">STAR ShiftLeader Trainings</a>'); }
		

		$('#check_id').hide();
		$.cookie('labid', data);
		$.cookie('inst', inst);
		$.cookie('pers', pers);
		$('body').append('<center><input type="button" name="continue" value="Proceed to ShiftSignup" id="proceed">');
		$('#proceed').click(function() {
		    window.location.href = "index.php";
		});
	    } else {
		$('#message').css({color: '#F00'}).text('ERROR: name does not match to ID, please check your input and try again');
			$('body').append('<center><input type="button" name="continue" value="Ignore and Proceed" id="proceed">');
			$('#proceed').click(function() {
		    	window.location.href = "index.php";
			});
			$.cookie('labid', '');
			$.cookie('inst', 0);
			$.cookie('pers', 0);
	    }
        });
    });
});
</script>
</head>
<body>

<center>
<h1>Please select your institution, your name and type in your BNL ID below</h1>

<FIELDSET style="display: inline-block; background-color: white;"> <LEGEND>please select your institution and user name - it will                                      
be automatically preset at the opening time</LEGEND>                                                                                                                   
    <select name="inst_id" id="inst" class="mod_select">                                                                                                               
        <option value="">--- Institutions ---</option>                                                                                                                 
        <?php foreach ($inst as $k => $v) { ?>                                                                                                                         
            <option value="i_<?=$v['id'] ?>"                                                                                                                           
        <?php $f = get_flag($v['cnt']); if (!empty($f)) { echo 'style="background-image:url(\'img/flags_sm/'.$f.'\');"'; } ?>                                          
        ><?=$v['name'] ?></option>                                                                                                                                     
        <?php } ?>                                                                                                                                                     
    </select>                                                                                                                                                          
    <select name="person_id" id="person">                                                                                                                              
        <option value="">------ People ------</option>                                                                                                                 
        <?php foreach($memb as $k => $v) { ?>                                                                                                                          
        <option value="<?=$v['Id'] ?>" class="i_<?=$v['InstitutionId'] ?>"><?=$v['LastName'] ?>, <?=$v['FirstName'] ?></option>                                        
        <?php } ?>                                                                                                                                                     
    </select>                                                                                                                                                          
    <input type="text" id="bnl_id" placeholder="your BNL ID" style="width: 220px;" />                                                                
</FIELDSET> 
<br><br>
<input type="button" id="check_id" name="check_id" value="CHECK ID">
<h2 id="message"></h2>
<?php 
 require('footer.tpl.php');
?>