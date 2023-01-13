<center>
<?php echo '<h2>Shifts signed up for by <a href="https://phonebook.sdcc.bnl.gov/sphenix/client/#mid:'.$mem[0]['Id'].'">'.$mem[0]['FirstName'].' '.$mem[0]['LastName'].'</a> are : </h2>'; ?>

<table id="st_shifts_sig">
<tr class="st_shifts_tbl_hdr"> 
    <td>Week</td> 
    <td>Hours</td>
    <td>Position</td>
</tr>
<?php
$tm = strtotime($run_start_date);

// remove unrealistic slots from shift list
if (!empty($shifts)) {
    foreach($shifts as $k => $v) {
	$id = intval($v['shiftTypeID']);
	if ($id < 1 || $id > 8) {
	    unset($shifts[$k]);
	}
    }
}

if (empty($shifts)) {
    echo '<tr><td colspan="3" align="center"><b>Has not signed up</b></td></tr>';
} else {
    
    $i = 0;
    foreach($shifts as $k => $v) {
	$tm_st = $tm + intval($v['week']) * 7 * 24 * 3600;
	$tm_en = $tm + (intval($v['week'])+1) * 7 * 24 * 3600;
	$i += 1;
	// date
	echo '<tr class="st_shifts_tbl_row ';
	if ($i % 2) { echo 'even'; } else { echo 'odd'; };
	echo '"><td><nobr>'.date('M j', $tm_st).'<small>'.date('S', $tm_st).'</small>'.
	' - '.date('M j', $tm_en).'<small>'.date('S',$tm_en).'</small></nobr></td>';
	
	// hours
	$hrs[0] = '0:00 - 8:00';
	$hrs[1] = '8:00 - 16:00';
	$hrs[2] = '16:00 - 0:00';
	if ( $v['shiftTypeID'] == 1 || $v['shiftTypeID'] == 8 ) {
	    echo '<td>N/A</td>';
	} else {
	    echo '<td>'.$hrs[$v['shiftNumber']].'</td>';
	}
	
	// position
	$pos[1] = 'Period Coordinator';
	$pos[2] = 'Shift Leader';
	$pos[3] = 'Detector Operator';
	$pos[4] = 'DAQ Operator';
	$pos[5] = 'Data Monitor Operator';
	$pos[6] = 'Shift Leader Trainee';
	$pos[7] = 'Detector Operator Trainee';
	$pos[8] = 'Oflline QA';
    	echo '<td>'.$pos[$v['shiftTypeID']].'</td></tr>';
    }
}
?>
<tr class="st_shifts_tbl_row"><td colspan="3" align="center"><b><font color="red">Note:</font> Select buttons will NOT appear for the above weeks on any of the signup sheets</b></td></tr>
<tr class="st_shifts_tbl_row"><td colspan="3" align="center"><small><a href="?do=edittraining&id=<?=$mem[0]['Id'] ?>">edit training records for this person</a></small></td></tr>
</table>


