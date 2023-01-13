<?php if (!empty($fake_db)) { ?>
<center><h2><font color="red">FAKE DATABASE</font></h2>
<?php } ?>
<?php 

if ($FORCE) { 
    echo '<center><h2><font color="red">*** FORCED SIGNUP MODE ***<br><i>leave this page immediately if you are not authorized for this task<br>click on <img src="img/icons/star.png" width="20" height="20"> symbols to force signup</i></font></h2></center>'; 
?>
<script>
jQuery.fn.center = function () {
    this.css("position","absolute");
    this.css("top", (($(window).height() - this.outerHeight()) / 2) + $(window).scrollTop() + "px");
    this.css("left", (($(window).width() - this.outerWidth()) / 2) + $(window).scrollLeft() + "px");
    return this;
}

function getWeekString(week) {
    var tm_st = <?php echo strtotime($run_start_date); ?> + week * 7 * 24 * 3600;
    var tm_en = <?php echo strtotime($run_start_date); ?> + (week+1) * 7 * 24 * 3600;
    var begin = new Date(tm_st * 1000);
    var end = new Date(tm_en * 1000);
    return begin.toDateString() + ' - ' + end.toDateString();
}

function getSlotType(shiftNumber) {
    switch(shiftNumber) {
	case 0:
	    return '0:00 - 8:00';
	    break;
	case 1:
	    return '8:00 - 16:00';
	    break;
	case 2:
	    return '16:00 - 0:00';
	    break;
    }
}

function getShiftType(shiftType) {
    switch(shiftType) {
	case 0:
	    return 'unknown (0)';
	    break;
	case 1:
	    return 'Period Coordinator';
	    break;
	case 2:
	    return 'Shift Leader';
	    break;
	case 3: 
	    return 'Detector Operator';
	    break;
	case 4:
	    return 'DAQ Operator';
	    break;
	case 5:
	    return 'Data Monitor Operator';
	    break;
	case 6: 
	    return 'Leader Trainee';
	    break;
	case 7:
	    return 'Det.Op.Trainee';
	    break;
	default:
	    return 'unknown '+shiftType;
	    break;
    }
}

function test(oldUserName, week, shiftNumber, shiftType) {
    if (oldUserName == '') { oldUserName = 'Empty Slot'; }
    $('#force_button_cancel').unbind('click');
    $('#force_button_cancel').click(function() {
	$(window).unbind('scroll');
	alert('cancelled');
	$('#confirm_force').hide();
    });
    $('#force_button_apply').unbind('click');
    $('#force_button_clear').click(function() {
	var test = confirm("Do you really want to free this slot? This means, person will be unsubscribed, and institution will loose credit for it");
	if (!test) { return; }
	$('#confirm_force').hide();
	$(window).unbind('scroll');
	var week = $('#force_week').val();
	var shiftNumber = $('#force_shiftNumber').val();
	var shiftTypeID = $('#force_shiftTypeID').val();
	var trainingCheck = $('#force_trainingCheck').val();
	var link = '?do=forcesignupclear&week='+week+'&shiftNumber='+shiftNumber+'&shiftTypeID='+shiftTypeID+'&trnCheck='+trainingCheck;
	window.location.replace(link);
    });
    $('#force_button_apply').click(function() {
	var week = $('#force_week').val();
	var shiftNumber = $('#force_shiftNumber').val();
	var shiftTypeID = $('#force_shiftTypeID').val();
	var replaceInst = $('#force_replaceInst').val();
	var personID      = $('#force_star_users').val();
	var trainingCheck = $('#force_trainingCheck').val();
	//alert('Apply: ' + week + ', ' + getSlotType(shiftNumber) + ', ' + shiftTypeID + ', ' + replaceInst + ' => ' + personID);
	$('#confirm_force').hide();
	$(window).unbind('scroll');
	var link = '?do=forcesignupinsert&week='+week+'&shiftNumber='+shiftNumber+'&shiftTypeID='+shiftTypeID+'&personID='+personID+'&replaceInst='+replaceInst+'&trnCheck='+trainingCheck;
	window.location.replace(link);
    });
    $('#force_week').val(week);
    if (shiftType == 1) {
	$('#force_shift_slot').html( 'week: <b>' + getWeekString(week) + '</b>, slot: <b>' + getShiftType(shiftType) + '</b>' );
    } else {
	$('#force_shift_slot').html( 'week: <b>' + getWeekString(week) + '</b>, shift: <b>' + getSlotType(shiftNumber) + '</b>, slot: <b>' + getShiftType(shiftType) + '</b>' );
    }
    $('#force_shiftNumber').val(shiftNumber);
    $('#force_shiftTypeID').val(shiftType);
    $('#star_user_name_old').html(oldUserName);
    $('#confirm_force').center().show();
    $(window).scroll(function() {
	$('#confirm_force').center();
    });
}
</script>
<div id="confirm_force" style="display: none; background-color: white; padding: 10px; border: 3px solid red;">
<table width="100%" cellpadding="10">
<tr align="center">
    <td>
    <h3>FORCED SET/REPLACE</h3>
    </td>
</tr>
<tr align="center">
<td>For <div id="force_shift_slot" style="display: inline-block;"></div></td>
</tr>
<tr align="center">
    <td align="left">
    <b>FROM:</b>&nbsp; <div id="star_user_name_old" style="display: inline-block"></div>
    </td>
</tr>
<tr align="center">
    <td align="left">
    <b>TO:</b>&nbsp; 
<?=$member_select ?>
    </td>
</tr>
<tr align="center">
    <td> 
    <SELECT name="replaceInst" id="force_replaceInst">
	<OPTION value="1">reassigning slot to new institution</OPTION>
	<OPTION value="2">keeping old institution credit</OPTION>
    </SELECT>
    </td>
</tr>
<tr align="center">
    <td> 
    <SELECT name="trainingCheck" id="force_trainingCheck" disabled=disabled>
	<OPTION value="1">check training validity, insert training record if needed</OPTION>
	<OPTION value="2">do not check/modify training records</OPTION>
    </SELECT>
    </td>
</tr>
<tr align="center">
    <td>
    <input type="button" name="cancel" value="Cancel" id="force_button_cancel" style="background-color: yellow; color: black; width: 120px;">
    &nbsp;
    <input type="button" name="apply" value="Apply/Replace" id="force_button_apply" style="background-color: green; color: white; width: 120px;">
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <input type="button" name="clear" value="Clear Slot" id="force_button_clear" style="background-color: red; color: white; width: 120px;">
    <input type="hidden" name="force_week" id="force_week" value="">
    <input type="hidden" name="force_shiftNumber" id="force_shiftNumber" value="">
    <input type="hidden" name="force_shiftTypeID" id="force_shiftTypeID" value="">
    </td>
</tr>
</table>
</div>
<?php
} 
?>
<?php
$cfg =& Config::Instance();
$warn_banner = $cfg->Get('run', 'display_warning_banner');
?>
<?php if (!empty($pid) && $pid > 0) { ?>
<FORM method="post" action="" name="signupTable">
<input type="hidden" name="personID" value="<?=$pid ?>">
<input type="hidden" name="personName" value="<?=$personName ?>">
<input type="hidden" name="institutionID" value="<?=$institutionID ?>">
<input type="hidden" name="institutionName" value="<?=$institutionName ?>">
<input type="hidden" name="do" value="submitsignup">
<input type="hidden" name="signupType" value="0">
<?php } ?>
<table id="st_shifts_tbl">
<?php 
$rw = $current_week;
if ($rw < 0) { $rw = 0; }
$tm = strtotime($run_start_date);
for ($i = 0; $i < $num_weeks_total; $i++) { 
if ( !empty($reduced) && ( ($i < ($rw - 1)) || ($i > ($rw + 1))) ) { continue; }
if ( $sharr[$i][0][0] != 'N/A' ) {
if ($current_week == $i || $rw == $i) {
?>
<tr class="st_shifts_tbl_hdr_current">
<?php } else { ?>
<tr class="st_shifts_tbl_hdr">
<?php } ?>
    <td width="14%"><a name="week_<?=$i ?>"></a>Week</td>
    <td width="14%">Period Coord.</td>
    <td width="14%">Shift</td>
    <td width="14%">Shift Leader</td>
    <td width="14%">Detector Opr.</td>
    <td width="14%">DAQ Opr.</td>
    <td width="15%">Data Monitor Opr</td>
		<!--
    <td>Leader Trainee</td>
    <td>Det.Opr.Trainee</td>
		//-->
</tr>
<?php } else if ($current_week == $i || $rw == $i) { ?>
<tr class="st_shifts_tbl_hdr_current"><td colspan=9>&nbsp;</td></tr>
<?php } ?>
<?php
 if ( $warn_banner ) {
    echo '<tr><td colspan="9"><div style="display: inline-block; text-align:center; width:100%; background-color: yellow; color: red;">SHIFT SIGNUP TEST MODE, ALL ENTRIES WILL BE WIPED OUT WHEN NORMAL SIGNUP STARTS</div></td></tr>';
 }
 if ( $sharr[$i][0][0] == 'N/A' ) {
		$tm_st = $tm + $i * 7 * 24 * 3600;
		$tm_en = $tm + ($i+1) * 7 * 24 * 3600;
  	 	echo '<tr class="st_shifts_tbl_row even">'
			.'<td colspan="9"><div style="display: inline-block; text-align:center; width:100%; background-color: gray; color: white;"><p><b>No operations week: '
			.date('M j', $tm_st).'<small>'.date('S', $tm_st).'</small> - '.date('M j', $tm_en).'<small>'.date('S',$tm_en).'</small>'.'</div>'
			.'</b></p></td></tr>';
		
 } else {
?>
<tr class="st_shifts_tbl_row even">
    <td rowspan="3" class="st_shifts_dt">
<?php
    $tm_st = $tm + $i * 7 * 24 * 3600;
    $tm_en = $tm + ($i+1) * 7 * 24 * 3600;
    echo '<nobr>'.date('M j', $tm_st).'<small>'.date('S', $tm_st).'</small> - '.date('M j', $tm_en).'<small>'.date('S',$tm_en).'</small></nobr>';
?>
    </td>
    <td rowspan="3" class="st_shifts_crd">
    <?php
	if ($FORCE) { 
	    $name = "";
	    if (!empty($sharr[$i][0][1]['personID'])) {
		$name = $mem[ $sharr[$i][0][1]['personID'] ]['FirstName'].' '.$mem[ $sharr[$i][0][1]['personID'] ]['LastName'].' - '. utf8_decode( $sharr[$i][0][1]['institution'] );
;	    }
	    echo '<img src="img/icons/star.png" width="20" height="20" onClick="test(\''.$name.'\', '.$i.',0,1);">'; 
	}
	if (!empty($sharr[$i][0][1]['personID'])) {
	    echo '<nobr><a target="_blank" href="'.$show_member_url.$sharr[$i][0][1]['personID'].'">'.$mem[ $sharr[$i][0][1]['personID'] ]['FirstName'].' '.$mem[ $sharr[$i][0][1]['personID'] ]['LastName'].
	    '</a></nobr><br><small>'. utf8_decode( $sharr[$i][0][1]['institution'] ).'</small>';
	    if ( $sharr[$i][0][1]['personID'] == $pid && !isset($weeks[$i]['lock']) ) {
		 echo '<br><input type="radio" name="week['.$i.']" value="-100"> <font color="red">remove</font>';
	    }
	} else if ($pid > 0 && empty($taken[$i])) {
	    if ( isset($ptrain[1]['allowed_from']) && $ptrain[1]['allowed_from'] <= $i && !isset($weeks[$i]['lock']) ) {
		echo '<input type="radio" name="week['.$i.']" value="100">';
	    }
	    //echo '<input type="radio" name="week['.$i.']" value="100">';
	}
    ?>
    &nbsp;</td>
    <td class="st_shifts_tm"><nobr>0:00-8:00</nobr></td>
    <?php for ($j = 2; $j < 6; $j++) { // SPHENIX = NO TRAINING SLOTS
	echo '<td';
	if ( !empty($sharr[$i][0][$j]) && $sharr[$i][0][$j] == 'N/A' ) {
	    echo ' class="st_disabled"'; 
	} else if ($j > 5) { echo ' class="st_trn"'; }
	echo '>';
	if ($FORCE) { 
	    $name = "";
	    if (!empty($sharr[$i][0][$j]['personID'])) {
		$name = $mem[ $sharr[$i][0][$j]['personID'] ]['FirstName'].' '.$mem[ $sharr[$i][0][$j]['personID'] ]['LastName'].' - '. utf8_decode( $sharr[$i][0][$j]['institution'] );
	    }
	    if (!( !empty($sharr[$i][0][$j]) && $sharr[$i][0][$j] == 'N/A' )) {
	    echo '<img src="img/icons/star.png" width="20" height="20" onClick="test(\''.$name.'\', '.$i.',0,'.$j.');">'; 
	    }
	}
	if (!empty($sharr[$i][0][$j]['personID'])) {
	    if ($sharr[$i][0][$j] != 'N/A') {

		// check if this person has taken shift on a previous week but at different time
		if ( !empty($sharr[$i-1]) ) {
		    for ( $tj = 2; $tj < 6; $tj++ ) { // SPHENIX = NO TRAINING SLOTS
			if ( 
			    ( isset($sharr[$i-1][1][$tj]['personID']) && @$sharr[$i][0][$j]['personID'] == @$sharr[$i-1][1][$tj]['personID'] )
			    || ( isset($sharr[$i-1][2][$tj]['personID']) && @$sharr[$i][0][$j]['personID'] == @$sharr[$i-1][2][$tj]['personID'] ) ) {
			    echo '<center><nobr><span style="background-color: '.( ( $j > 5 || $tj > 5 ) ? "#999999" : "#FF9900" ).'; color: #FFF;"><b>SHIFT OVERLAPS</b></span></nobr></center>';
			}
		    }
		}

		echo '<a target="_blank" href="'.$show_member_url.$sharr[$i][0][$j]['personID'].'">'.$mem[ $sharr[$i][0][$j]['personID'] ]['FirstName'].' '.$mem[ $sharr[$i][0][$j]['personID'] ]['LastName'].
		'</a><br><small>'.utf8_decode( $sharr[$i][0][$j]['institution'] ).'</small>';
		if ($sharr[$i][0][$j]['personID'] == $pid) {
		    if ($j == 6 && intval($ptrain[2]['locked']) == 1) { 
		    } else if ($j == 7 && intval($ptrain[3]['locked']) == 1) {
		    } else if ( !isset($weeks[$i]['lock']) ) {
			echo '<br><input type="radio" name="week['.$i.']" value="-'.$j.'00"> <font color="red">remove</font>';
		    }
		}
	    }
	} else if ( $pid > 0 && $i > $current_week) {
	  if ( !empty($sharr[$i][0][$j]) && $sharr[$i][0][$j] == 'N/A' ) {
	  
	  } else {
	    if ( empty($taken[$i]) ) {
    	    if ( $j == 5 && !isset($weeks[$i]['lock']) ) {
						echo '<input type="radio" name="week['.$i.']" value="'.$j.'00" data-test="bla">';
	    		}
			    if ( ($j == 3 || $j == 4) && isset($ptrain[3]['allowed_from']) && $ptrain[3]['allowed_from'] <= $i && !isset($weeks[$i]['lock']) ) {
						echo '<input type="radio" name="week['.$i.']" value="'.$j.'00" data-test="bla">';
			    }
			    if ( $j == 2 && isset($ptrain[2]['allowed_from']) && $ptrain[2]['allowed_from'] <= $i  && !isset($weeks[$i]['lock']) ) {
						echo '<input type="radio" name="week['.$i.']" value="'.$j.'00" data-test="bla">';
			    }
			}
	  	if ( $j == 6 && !isset($ptrain[2]['allowed_from']) && !isset($weeks[$i]['lock']) ) {
				echo '<input type="radio" name="week['.$i.']" value="'.$j.'00" data-test="bla">';
			}
	  	if ( $j == 7 && !isset($ptrain[3]['allowed_from']) && !isset($weeks[$i]['lock']) ) {
				echo '<input type="radio" name="week['.$i.']" value="'.$j.'00" data-test="bla">';
	    }
	    if (!empty($shvac[$i][0][$j])) {
				echo '<br><i><small><font color="red">'.$shvac[$i][0][$j].'</font></small></i>';
	    }
	  }
	} else {
	    if (!empty($shvac[$i][0][$j])) {
		echo '<br><i><small><font color="red">'.$shvac[$i][0][$j].'</font></small></i>';
	    }
	}
	echo '&nbsp;</td>';
	};
    ?>
</tr>
<tr class="st_shifts_tbl_row odd">
    <td class="st_shifts_tm"><nobr>8:00-16:00</nobr></td>
    <?php for ($j = 2; $j < 6; $j++) { // SPHENIX = NO TRAINING SLOTS
	echo '<td';
	if ( !empty($sharr[$i][0][$j]) && $sharr[$i][0][$j] == 'N/A' ) {
	    echo ' class="st_disabled"'; 
	} else if ($j > 5) { echo ' class="st_trn"'; };
	echo '>';
	if ($FORCE) { 
	    $name = "";
	    if (!empty($sharr[$i][1][$j]['personID'])) {
		$name = $mem[ $sharr[$i][1][$j]['personID'] ]['FirstName'].' '.$mem[ $sharr[$i][1][$j]['personID'] ]['LastName'].' - '.utf8_decode( $sharr[$i][1][$j]['institution'] );
	    }
	    if (!( !empty($sharr[$i][0][$j]) && $sharr[$i][0][$j] == 'N/A' )) {
	    echo '<img src="img/icons/star.png" width="20" height="20" onClick="test(\''.$name.'\', '.$i.',1,'.$j.');">'; 
	    }
	}
	if (!empty($sharr[$i][1][$j]['personID'])) {


	    if ($sharr[$i][1][$j] != 'N/A' ) {

		// check if this person has taken shift on a previous week but at different time
		if ( !empty($sharr[$i-1]) ) {
		    for ( $tj = 2; $tj < 6; $tj++ ) { // SPHENIX = NO TRAINING SLOTS
			if ( 
			    ( isset($sharr[$i-1][0][$tj]['personID']) && @$sharr[$i][1][$j]['personID'] == @$sharr[$i-1][0][$tj]['personID'] )
			    || ( isset($sharr[$i-1][2][$tj]['personID']) && @$sharr[$i][1][$j]['personID'] == @$sharr[$i-1][2][$tj]['personID'] ) ) {
			    echo '<center><nobr><span style="background-color: '.( ( $j > 5 || $tj > 5 ) ? "#999999" : "#FF9900" ).'; color: #FFF;"><b>SHIFT OVERLAPS</b></span></nobr></center>';
			}
		    }
		}

		echo '<a target="_blank" href="'.$show_member_url.$sharr[$i][1][$j]['personID'].'">'.$mem[ $sharr[$i][1][$j]['personID'] ]['FirstName'].' '.$mem[ $sharr[$i][1][$j]['personID'] ]['LastName'].
		'</a><br><small>'. utf8_decode( $sharr[$i][1][$j]['institution'] ).'</small>';
		if ($sharr[$i][1][$j]['personID'] == $pid) {
		    if ($j == 6 && intval($ptrain[2]['locked']) == 1) { 
		    } else if ($j == 7 && intval($ptrain[3]['locked']) == 1) {
		    } else if ( !isset($weeks[$i]['lock']) ) {
			echo '<br><input type="radio" name="week['.$i.']" value="-'.$j.'01"> <font color="red">remove</font>';
		    }
		}
	    }
	} else if ($pid > 0 && $i > $current_week) {
	  if ( !empty($sharr[$i][0][$j]) && $sharr[$i][0][$j] == 'N/A' ) {
	  
	  } else {
	   if ( empty($taken[$i]) ) {
	    if ( $j == 5  && !isset($weeks[$i]['lock']) ) {
		echo '<input type="radio" name="week['.$i.']" value="'.$j.'01">';
	    }
	    if ( ($j == 3 || $j == 4) && isset($ptrain[3]['allowed_from']) && $ptrain[3]['allowed_from'] <= $i  && !isset($weeks[$i]['lock']) ) {
		echo '<input type="radio" name="week['.$i.']" value="'.$j.'01">';
	    }
	    if ( $j == 2 && isset($ptrain[2]['allowed_from']) && $ptrain[2]['allowed_from'] <= $i  && !isset($weeks[$i]['lock']) ) {
		echo '<input type="radio" name="week['.$i.']" value="'.$j.'01">';
	    }
	  }
	    if ( $j == 6 && !isset($ptrain[2]['allowed_from']) && !isset($weeks[$i]['lock']) ) {
		echo '<input type="radio" name="week['.$i.']" value="'.$j.'01">';
	    }
	    if ( $j == 7 && !isset($ptrain[3]['allowed_from']) && !isset($weeks[$i]['lock']) ) {
		echo '<input type="radio" name="week['.$i.']" value="'.$j.'01">';
	    }
	    if (!empty($shvac[$i][1][$j])) {
		echo '<br><i><small><font color="red">'.$shvac[$i][1][$j].'</font></small></i>';
	    }
	  }
	} else { 
	    if (!empty($shvac[$i][1][$j])) {
		echo '<br><i><small><font color="red">'.$shvac[$i][1][$j].'</font></small></i>';
	    }
	}
	echo '&nbsp;</td>';
	};
    ?>
</tr>
<tr class="st_shifts_tbl_row even">
    <td class="st_shifts_tm"><nobr>16:00-00:00</nobr></td>
    <?php for ($j = 2; $j < 6; $j++) { // SPHENIX = NO TRAINING SLOTS
	echo '<td';
	if ( !empty($sharr[$i][0][$j]) && $sharr[$i][0][$j] == 'N/A' ) {
	    echo ' class="st_disabled"'; 
	} else if ($j > 5) { echo ' class="st_trn"'; };
	echo '>';
	if ($FORCE) { 
	    $name = "";
	    if (!empty($sharr[$i][2][$j]['personID'])) {
		$name = $mem[ $sharr[$i][2][$j]['personID'] ]['FirstName'].' '.$mem[ $sharr[$i][2][$j]['personID'] ]['LastName'].' - '.utf8_decode( $sharr[$i][2][$j]['institution'] );
	    }
	    if (!( !empty($sharr[$i][0][$j]) && $sharr[$i][0][$j] == 'N/A' )) {
	    echo '<img src="img/icons/star.png" width="20" height="20" onClick="test(\''.$name.'\', '.$i.',2,'.$j.');">'; 
	    }
	}
	if (!empty($sharr[$i][2][$j]['personID'])) {
	    if ($sharr[$i][2][$j] != 'N/A') {

		// check if this person has taken shift on a previous week but at different time
		if ( !empty($sharr[$i-1]) ) {
		    for ( $tj = 2; $tj < 6; $tj++ ) { // SPHENIX = NO TRAINING SLOTS
			if ( 
			    ( isset($sharr[$i-1][0][$tj]['personID']) && @$sharr[$i][2][$j]['personID'] == @$sharr[$i-1][0][$tj]['personID'] )
			    || ( isset($sharr[$i-1][1][$tj]['personID']) && @$sharr[$i][2][$j]['personID'] == @$sharr[$i-1][1][$tj]['personID'] ) ) {
			    echo '<center><nobr><span style="background-color: '.( ( $j > 5 || $tj > 5 ) ? "#999999" : "#FF9900" ).'; color: #FFF;"><b>SHIFT OVERLAPS</b></span></nobr></center>';
			}
		    }
		}


		echo '<a target="_blank" href="'.$show_member_url.$sharr[$i][2][$j]['personID'].'">'.$mem[ $sharr[$i][2][$j]['personID'] ]['FirstName'].' '.$mem[ $sharr[$i][2][$j]['personID'] ]['LastName'].
		'</a><br><small>'.utf8_decode( $sharr[$i][2][$j]['institution'] ).'</small>';
		if ($sharr[$i][2][$j]['personID'] == $pid) {
		    if ($j == 6 && intval($ptrain[2]['locked']) == 1) { 
		    } else if ($j == 7 && intval($ptrain[3]['locked']) == 1) {
		    } else if ( !isset($weeks[$i]['lock']) ) {
			echo '<input type="radio" name="week['.$i.']" value="-'.$j.'02"> <font color="red">remove</font>';
		    }
		}
	    }
	} else if ($pid > 0 && $i > $current_week) {
	  if ( !empty($sharr[$i][0][$j]) && $sharr[$i][0][$j] == 'N/A' ) {
	  
	  } else {
		 if ( empty($taken[$i]) ) {
	    if ( $j == 5  && !isset($weeks[$i]['lock']) ) {
		echo '<input type="radio" name="week['.$i.']" value="'.$j.'02">';
	    }
	    if ( ($j == 3 || $j == 4) && isset($ptrain[3]['allowed_from']) && $ptrain[3]['allowed_from'] <= $i  && !isset($weeks[$i]['lock']) ) {
		echo '<input type="radio" name="week['.$i.']" value="'.$j.'02">';
	    }
	    if ( $j == 2 && isset($ptrain[2]['allowed_from']) && $ptrain[2]['allowed_from'] <= $i  && !isset($weeks[$i]['lock']) ) {
		echo '<input type="radio" name="week['.$i.']" value="'.$j.'02">';
	    }
	  }
	    if ( $j == 6 && !isset($ptrain[2]['allowed_from']) && !isset($weeks[$i]['lock']) ) {
		echo '<input type="radio" name="week['.$i.']" value="'.$j.'02">';
	    }
	    if ( $j == 7 && !isset($ptrain[3]['allowed_from']) && !isset($weeks[$i]['lock']) ) {
		echo '<input type="radio" name="week['.$i.']" value="'.$j.'02">';
	    }
	    if (!empty($shvac[$i][2][$j])) {
		echo '<br><i><small><font color="red">'.$shvac[$i][2][$j].'</font></small></i>';
	    }
	  }
	} else {
	    if (!empty($shvac[$i][2][$j])) {
		echo '<br><i><small><font color="red">'.$shvac[$i][2][$j].'</font></small></i>';
	    }
	}
	echo '&nbsp;</td>';
	};
    ?>
</tr>
<?php } ?>
<?php if ($current_week == $i) { ?>
<tr class="st_shifts_tbl_hdr_current"><td colspan="9">&nbsp;</td></tr>
<?php } ?>
<?php if ( (!empty($pid) && $pid > 0) && ( $sharr[$i][0][0] != 'N/A' ) ) { ?>
<tr><td colspan="9" class="st_hr" align="center"><input type="submit" name="signup"  font=-2 value="Submit Signup for <?=$mem[$pid]['FirstName'] ?> <?=$mem[$pid]['LastName'] ?>"></td></tr>
<?php } elseif ( $sharr[$i][0][0] != 'N/A' ) { ?>
<tr><td colspan="9" class="st_hr"><hr></td></tr>
<?php 
   } } // num_weeks_total 
?>
</table>
<?php if (!empty($pid) && $pid > 0) { ?>
</FORM>
<?php } ?>
