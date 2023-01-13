<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">                                                                              
<html>                                                                                                                                                                  
<head>                                                                                                                                                                  
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">                                                                                                      
<title>sPHENIX ShiftSignup Training Check</title>                                                                                                                          
<link href="css/styles.css" type="text/css" rel="stylesheet">                                                                                                           
<script type="text/javascript" src="js/jquery-1.4.4.min.js"></script>                                                                                                   
<script type="text/javascript" src="js/jquery.cookie.js"></script>                                                                                                      
<script>
function check_person_id(e){
    var inst = parseInt($(e).attr('data-inid'));
    var pers = parseInt($(e).attr('data-phid'));
    var labid = $('#bnlid_'+pers).val();
    if (inst == undefined || inst == 0 || pers == undefined || pers == 0 || labid.length == 0) {
	alert('missing parameter, check Lab ID field!');
	return false;
    }
    $.getJSON('index.php?do=bnlcheck_check&inst='+inst+'&pers='+pers+'&labid='+labid+'&ignore=true', function( data, status, xhr ) {
        if (data.error == undefined) {
	    console.log(data);
	    if (data.itd != undefined && data.itd.name_first != undefined) {
		$(e).attr('value', data.itd.name_last+', '+data.itd.name_first+' ACCEPTED');
	    } else {
		$(e).attr('value', 'CHECK FAILED, POSSIBLE ID MISMATCH');
	    }
	} else {
	    console.log('ERROR: broken data received!');
	}
    });
};

$(document).ready(function(){
    $('.person_check_button').click(function() {
	check_person_id(this);
    });
    $('#loc_reload').click(function() {
	location.reload();
    });
});


</script>
<body>

<?php
 require('missingid.body.tpl.php');
 require('footer.tpl.php');
