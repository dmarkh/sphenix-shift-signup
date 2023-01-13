<?php
$cfg =& Config::Instance();
$nswi       = explode(',', $cfg->Get('run', 'noop_shift_week_ids'        ));                                                                                                                                             
$warn_banner = $cfg->Get('run', 'display_warning_banner');                                                                                                                                                                   
 if ( $warn_banner ) {                                                                                                                                                  
    echo '<p><div style="display: inline-block; text-align:center; width:100%; background-color: yellow; color: red;">SHIFT SIGNUP TEST MODE, ALL ENTRIES WILL BE WIPED OUT WHEN NORMAL SIGNUP STARTS</div></p>'; 
 }                                                                                                                                                                      
?> 
<?php if (!empty($personID)) { ?>
<FORM method="post" action="">
<input type="hidden" name="personID" value="<?=$personID ?>">
<input type="hidden" name="personName" value="<?=$personName ?>">
<input type="hidden" name="institutionID" value="<?=$institutionID ?>">
<input type="hidden" name="institutionName" value="<?=$institutionName ?>">
<input type="hidden" name="displayNum" value="2">
<input type="hidden" name="do" value="submitsignup">
<?php } ?>

<center>
<table id="st_shifts_qa">
<tr class="st_shifts_tbl_hdr"> 
    <td colspan="3">Period</td> 
    <td>Offline QA</td>
</tr> 
<?php
$tm = strtotime($run_start_date);
for ($i = $offline_qa_delay; $i < $num_weeks_total; $i++) { 
?> 
<tr class="st_shifts_tbl_row <?php if ($i%2) { echo 'even'; } else { echo 'odd'; }; ?>"> 
    <td class="st_shifts_dt"> 
<?php
    $tm_st = $tm + $i * 7 * 24 * 3600;
    echo '<nobr>'.date('M j', $tm_st).'<small>'.date('S', $tm_st).'</small>';
?> 
    </td>    
    <td class="st_shifts_dt">-</td>
    <td class="st_shifts_dt">
<?php
    $tm_en = $tm + ($i+1) * 7 * 24 * 3600;
    echo date('M j', $tm_en).'<small>'.date('S',$tm_en).'</small></nobr>';
?>    
    </td>
<?php if ( !in_array( $i, $nswi ) ) { ?>
    <td <?php 
    if ( !empty( $sharr[$i] ) ) {
	if ( empty( $shtrn[ $sharr[$i]['personID'] ] ) || empty( $shtrn[ $sharr[$i]['personID'] ]['beginTime'] ) ) {
	    echo 'style="background-color: #FFCDE1;" title="Training is missing"';
	} else if ( $shtrn[ $sharr[$i]['personID'] ]['isPending'] == 1 ) {
	    echo 'style="background-color: #FFCDE1;" title="Training is pending"';
	} else {
	    if ( ( time() - strtotime( $shtrn[ $sharr[$i]['personID'] ]['beginTime'] ) ) > ( 5 * 365 * 86400 ) ) {
		echo 'style="background-color: #FFD79B;" title="Training is too old: '.$shtrn[ $sharr[$i]['personID'] ]['beginTime'].'"';
	    } else {
		echo 'title="Training: '.$shtrn[ $sharr[$i]['personID'] ]['beginTime'].'"';
	    }
	}
    }
?>>
<?php
    if (!empty($sharr[$i])) {
        echo '<a href="'.$show_member_url.$sharr[$i]['personID'].'">'.$mem[ $sharr[$i]['personID'] ]['FirstName'].' '.$mem[ $sharr[$i]['personID'] ]['LastName'].
        '</a><br><small>'.$sharr[$i]['institution'].'</small>';
	if ($sharr[$i]['personID'] == $personID) {
	echo ' <input type="checkbox" name="week['.$i.']" value="-800"> <font color="red">remove</font>';
	}
    } else if (!empty($personID)) {
	echo '<input type="checkbox" name="week['.$i.']" value="800">';
    }   
?>
<?php } else { ?>
	<td class='st_shifts_td' style="background-color: gray; color: white;"><b>No operations week</b></td>
<?php } ?>
    </td>
</tr>
<?php } ?>
</table>

<?php if (!empty($personID)) { ?>
<INPUT type="submit" name="submit" value="Submit Signup for <?=$personName ?>">
</FORM>
<?php } ?>

<?php
 if ( $warn_banner ) {                                                                                                                                                  
    echo '<p><div style="display: inline-block; text-align:center; width:100%; background-color: yellow; color: red;">SHIFT SIGNUP TEST MODE, ALL ENTRIES WILL BE WIPED OUT WHEN NORMAL SIGNUP STARTS</div></p>'; 
 }                                                                                                                                                                      
?>
