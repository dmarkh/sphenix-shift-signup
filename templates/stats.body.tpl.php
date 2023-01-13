<table> 
<tr><td>

<table id="st_shifts_tbl" style="width: 80%; margin-left: 20px; border-left: 1px solid black; border-right: 1px solid black;">
<tr class="st_shifts_tbl_hdr">
    <td>Shift Type</td>
    <td>Open Slots</td>
    <td>Open Slots, %</td>
    <td>Total Slots</td>
</tr>
<tr align="center" class="odd">
    <td align="left">Coordinator</td>
    <td><?=$openCT ?></td>
    <td><?=$perCT ?>%</td>
    <td><?=$totalCT ?></td>
</tr>
<tr align="center" class="odd">
    <td align="left">Shift Leader</td>
    <td><?=$openSL ?></td>
    <td><?=$perSL ?>%</td>
    <td><?=$totalSL ?></td>
</tr>
<tr align="center" class="even">
    <td align="left">Detector Operator</td>
    <td><?=$openDO ?></td>
    <td><?=$perDO ?>%</td>
    <td><?=$totalDO ?></td>
</tr>
<tr align="center" class="odd">
    <td align="left">Data Monitor</td>
    <td><?=$openRT ?></td>
    <td><?=$perRT ?>%</td>
    <td><?=$totalRT ?></td>
</tr>
<!--
<tr align="center" class="even">
    <td align="left">Offline QA</td>
    <td><?=$openQA ?></td>
    <td><?=$perQA ?>%</td>
    <td><?=$totalQA ?></td>
</tr>
//-->
<tr align="center" style="background-color: brown; color: white;">
    <td align="left">SUMMARY NON-TRAINEE</td>
    <td><?=$openSumNT ?></td>
    <td><?php echo round( 100 * ($openSumNT) / ($totalSumNT), 1 ).'%'; ?></td>
    <td><?=$totalSumNT ?></td>
</tr>
<!--
<tr align="center" class="st_trn">
    <td align="left">Sh.L. Trainee</td>
    <td><?=$openSLT ?></td>
    <td><?=$perSLT ?>%</td>
    <td><?=$totalSLT ?></td>
</tr>
<tr align="center" class="st_trn">
    <td align="left">Det.Op. Trainee</td>
    <td><?=$openDOT ?></td>
    <td><?=$perDOT ?>%</td>
    <td><?=$totalDOT ?></td>
</tr>
<tr align="center" style="background-color: brown; color: white;">
    <td align="left">SUMMARY TRAINEE</td>
    <td><?php echo ($openSum - $openSumNT); ?></td>
    <td><?php echo round( 100 * ($openSum - $openSumNT) / ($totalSum - $totalSumNT), 1 ).'%'; ?></td>
    <td><?php echo ($totalSum - $totalSumNT); ?></td>
</tr>
<tr><td colspan="4"><hr></td></tr>
<tr align="center" style="background-color: brown; color: white;">
    <td align="left">GLOBAL SUMMARY</td>
    <td><?=$openSum ?></td>
    <td><?php echo round( 100 * ($openSum) / ($totalSum), 1 ).'%'; ?></td>
    <td><?=$totalSum ?></td>
</tr>
//-->
<?php if (isset($npeople)) { ?>
<tr><td colspan="3" align="right">Total number of unique persons signed for shifts: </td><td align="center"><?=$npeople ?></td></tr>
<?php } ?>
</table>

</td><td>
<p>Please select institution and required shift slots (see checkboxes below), then click [SEARCH!]</p>
<FORM name="frm" action="" method="post">
<SELECT name="inst_name">
<OPTION value="0" 
<?php
    if ($inst_selected == 0) { echo 'selected=selected'; };
    echo '>---SELECT INSTITUTION---</OPTION>';
    echo '<OPTION value="-1" ';
    if ($inst_selected == -1) { echo 'selected=selected'; };
    echo '>*** ALL INSTITUTIONS ***</OPTION>';
    foreach($inst as $k => $v) {
        echo '<OPTION value="'.$k.'" ';
        if ($inst_selected == $k) { echo 'selected=selected'; };
        echo '>'.$v['name'].'</OPTION>';
    }
?>
</SELECT>
<INPUT type="submit" name="submit" value="Search!">
<FIELDSET><LEGEND>Choose Extra Options</LEGEND>
    <INPUT type="checkbox" name="show_experts"
        <?php if ($showExperts) { echo 'checked=checked'; }; ?>
    > Show experts
    <INPUT type="checkbox" name="has_no_shifts" 
        <?php if ($hasNoShifts) { echo 'checked=checked'; }; ?>
    > Has not taken shifts yet
    <INPUT type="checkbox" name="show_authors_only" 
        <?php if ($showAuthorsOnly) { echo 'checked=checked'; }; ?>
    > Show Authors only
    <INPUT type="checkbox" name="suppress_stats" 
        <?php if ($suppressStats) { echo 'checked=checked'; }; ?>
    > Suppress Stats
    </FIELDSET>
    <FIELDSET><LEGEND>Choose Shift Type</LEGEND>
    <INPUT type="checkbox" name="shift_type[0]"
    <?php if ($kShiftType[0]) {  echo 'checked=checked'; } ?>
    > Shift Leader
    <INPUT type="checkbox" name="shift_type[1]"
    <?php if ($kShiftType[1]) {  echo 'checked=checked'; } ?>
    > Detector Operator
    <INPUT type="checkbox" name="shift_type[2]"
    <?php if ($kShiftType[2]) {  echo 'checked=checked'; } ?>
    > Data Monitor
    <INPUT type="checkbox" name="shift_type[3]"
    <?php if ($kShiftType[3]) {  echo 'checked=checked'; } ?>
    > Shift Leader Trainee
    <INPUT type="checkbox" name="shift_type[4]"
    <?php if ($kShiftType[4]) {  echo 'checked=checked'; } ?>
    > Detector Operator Trainee
    <INPUT type="checkbox" name="shift_type[5]"
    <?php if ($kShiftType[5]) {  echo 'checked=checked'; } ?>
    > Offline QA
    </FIELDSET>
    <FIELDSET><LEGEND>Legend</LEGEND>
    <font color="blue"><b>blue</b></font> : already signed up for shifts<br>
    <font color="green"><b>green</b></font> : run expert<br>
    <font color="black"><b>black</b></font> : did not sign up yet<br>
    </FIELDSET>
    <input type="hidden" name="do" value="stats">
    </FORM>

</td></tr>
<tr><td colspan="2">
<?php
    if (!empty($kShiftType[0]) && !empty($inst_selected) ) {
?>
<center>
<h2>Shift Leader availabilities: </h2>
<table class="tbl"><tr class="st_shifts_tbl_hdr"><td>Last name, First name</td><td>Institution</td><td>Email</td><td>Phone</td><td>Current Shifts</td><td>Notes / Past Shifts</td></tr>
<?php 
    $col[0] = 'odd';
    $col[1] = 'even';
    $i = 1;
    foreach($canSL as $k => $v) {
        echo '<tr class="'.$col[$i++ % 2].'"><td>';
	if (strtolower($v['isExpert']) == 'y') { echo '<b><font color="green">'; }
	echo $v['LastName'].', '.$v['FirstName'];
	if (strtolower($v['isExpert']) == 'y') { echo '</font></b>'; }
	echo '</td><td>'.$v['InstitutionName'].'</td><td>'.$v['EmailAddress'].'</td>';
   	echo '<td>'.$v['Phone'].'</td>';
	if ( !isset($v['total_shifts_signed']) ) {
		echo '<td>Total: <b>0</b></td>';
	} else {
		echo '<td>Total: <b>'.$v['total_shifts_signed'] . '</b> / Taken: '.$v['shifts_processed'].
			' / Left: ' .$v['shifts_unprocessed'].'</td>';
	}
	if ( !$suppressStats ) {
	    if (!empty($v['past_shiftstaken'])) {
		echo '<td>SUM: <font color="red"><b>'.array_sum($v['past_shiftstaken']).'</b></font>, PY0: <b>'.intval($v['past_shiftstaken'][0]).'</b>, PY1: <b>'.intval($v['past_shiftstaken'][1]).'</b>, PY2: <b>'.intval($v['past_shiftstaken'][2]).'</b></td>';
	    } else {
		echo '<td>SUM: <font color="red"><b>0</b></font>, no past entries found..</td>';
	    }
	} else {
		echo '<td></td>';
	}
	echo '</tr>';
    }
?>
</table><BR>
<?php } ?>

<?php
    if (!empty($kShiftType[1]) && !empty($inst_selected) ) {
?>
<center>
<h2>Detector Operator availabilities: </h2>
<table class="tbl"><tr class="st_shifts_tbl_hdr"><td>Last name, First name</td><td>Institution</td><td>Email</td><td>Phone</td><td>Current Shifts</td><td>Notes</td></tr>
<?php 
    $i = 1;
    foreach($canDO as $k => $v) {
        echo '<tr class="'.$col[$i++ % 2].'"><td>';
	if (strtolower($v['isExpert']) == 'y') { echo '<b><font color="green">'; }
	echo $v['LastName'].', '.$v['FirstName'];
	if (strtolower($v['isExpert']) == 'y') { echo '</font></b>'; }
	echo '</td><td>'.$v['InstitutionName'].'</td><td>'.$v['EmailAddress'].'</td>';
   	echo '<td>'.$v['Phone'].'</td>';
    if ( !isset($v['total_shifts_signed']) ) {
        echo '<td>Total: <b>0</b></td>';
    } else {
        echo '<td>Total: <b>'.$v['total_shifts_signed'] . '</b> / Taken: '.$v['shifts_processed'].
            ' / Left: ' .$v['shifts_unprocessed'].'</td>';
    }
	if ( !$suppressStats ) {
	    if (!empty($v['past_shiftstaken'])) {
		echo '<td>SUM: <font color="red"><b>'.array_sum($v['past_shiftstaken']).'</b></font>, PY0: <b>'.intval($v['past_shiftstaken'][0]).'</b>, PY1: <b>'.intval($v['past_shiftstaken'][1]).'</b>, PY2: <b>'.intval($v['past_shiftstaken'][2]).'</b></td>';
	    } else {
		echo '<td>SUM: <font color="red"><b>0</b></font>, no past entries found..</td>';
	    }
	} else {
		echo '<td></td>';
	}
	echo '</tr>';
    }
?>
</table><BR>
<?php } ?>

<?php
    if (!empty($kShiftType[2]) && !empty($inst_selected) ) {
?>
<center>
<h2>Crew availabilities: </h2>
<table class="tbl"><tr class="st_shifts_tbl_hdr"><td>Last name, First name</td><td>Institution</td><td>Email</td><td>Phone</td><td>Current Shifts</td><td>Notes</td></tr>
<?php 
    $i = 1;
    foreach($canRT as $k => $v) {
        echo '<tr class="'.$col[$i++ % 2].'"><td>';
	if (strtolower($v['isExpert']) == 'y') { echo '<b><font color="green">'; }
	echo $v['LastName'].', '.$v['FirstName'];
	if (strtolower($v['isExpert']) == 'y') { echo '</font></b>'; }
	echo '</td><td>'.$v['InstitutionName'].'</td><td>'.$v['EmailAddress'].'</td>';
    	    echo '<td>'.$v['Phone'].'</td>';

    if ( !isset($v['total_shifts_signed']) ) {
        echo '<td>Total: <b>0</b></td>';
    } else {
        echo '<td>Total: <b>'.$v['total_shifts_signed'] . '</b> / Taken: '.$v['shifts_processed'].
            ' / Left: ' .$v['shifts_unprocessed'].'</td>';
    }
	if ( !$suppressStats ) {
	    if (!empty($v['past_shiftstaken'])) {
		echo '<td>SUM: <font color="red"><b>'.array_sum($v['past_shiftstaken']).'</b></font>, PY0: <b>'.intval($v['past_shiftstaken'][0]).'</b>, PY1: <b>'.intval($v['past_shiftstaken'][1]).'</b>, PY2: <b>'.intval($v['past_shiftstaken'][2]).'</b></td>';
	    } else {
		echo '<td>SUM: <font color="red"><b>0</b></font>, no past entries found..</td>';
	    }
	} else {
		echo '<td></td>';
	}
	echo '</tr>';
    }
?>
</table><BR>
<?php } ?>

<?php
    if (!empty($kShiftType[3]) && !empty($inst_selected) ) {
?>
<center>
<h2>Shift Leader Trainee availabilities: </h2>
<table class="tbl"><tr class="st_shifts_tbl_hdr"><td>Last name, First name</td><td>Institution</td><td>Email</td><td>Phone</td><td>Notes</td></tr>
<?php 
    $i = 1;
    foreach($canSLT as $k => $v) {
        echo '<tr class="'.$col[$i++ % 2].'"><td>';
	if (strtolower($v['isExpert']) == 'y') { echo '<b><font color="green">'; }
	echo $v['LastName'].', '.$v['FirstName'];
	if (strtolower($v['isExpert']) == 'y') { echo '</font></b>'; }
	echo '</td><td>'.$v['InstitutionName'].'</td><td>'.$v['EmailAddress'].'</td><td>'.$v['Phone'].'</td>';
	if (!empty($v['past_shiftstaken'])) {
	    echo '<td>SUM: <font color="red"><b>'.array_sum($v['past_shiftstaken']).'</b></font>, PY0: <b>'.intval($v['past_shiftstaken'][0]).'</b>, PY1: <b>'.intval($v['past_shiftstaken'][1]).'</b>, PY2: <b>'.intval($v['past_shiftstaken'][2]).'</b></td>';
	} else {
	    echo '<td>SUM: <font color="red"><b>0</b></font>, no past entries found..</td>';
	}
	echo '</tr>';
    }
?>
</table><BR>
<?php } ?>

<?php
    if (!empty($kShiftType[4]) && !empty($inst_selected) ) {
?>
<center>
<h2>Detector Operator Trainee availabilities: </h2>
<table class="st_shifts_tbl"><tr class="st_shifts_tbl_hdr"><td>Last name, First name</td><td>Institution</td><td>Email</td><td>Phone</td><td>Notes</td></tr>
<?php 
    $i = 1;
    foreach($canDOT as $k => $v) {
        echo '<tr class="'.$col[$i++ % 2].'"><td>';
	if (strtolower($v['isExpert']) == 'y') { echo '<b><font color="green">'; }
	echo $v['LastName'].', '.$v['FirstName'];
	if (strtolower($v['isExpert']) == 'y') { echo '</font></b>'; }
	echo '</td><td>'.$v['InstitutionName'].'</td><td>'.$v['EmailAddress'].'</td><td>'.$v['Phone'].'</td>';
	if (!empty($v['past_shiftstaken'])) {
	    echo '<td>SUM: <font color="red"><b>'.array_sum($v['past_shiftstaken']).'</b></font>, PY0: <b>'.intval($v['past_shiftstaken'][0]).'</b>, PY1: <b>'.intval($v['past_shiftstaken'][1]).'</b>, PY2: <b>'.intval($v['past_shiftstaken'][2]).'</b></td>';
	} else {
	    echo '<td>SUM: <font color="red"><b>0</b></font>, no past entries found..</td>';
	}
	echo '</tr>';
    }
?>
</table><BR>
<?php } ?>

<?php
    if (!empty($kShiftType[5]) && !empty($inst_selected) ) {
?>
<center>
<h2>Offline QA availabilities: </h2>
<table class="st_shifts_tbl"><tr class="st_shifts_tbl_hdr"><td>Last name, First name</td><td>Institution</td><td>Email</td><td>Phone</td><td>Notes</td></tr>
<?php 
    $i = 1;
    foreach($canQA as $k => $v) {
        echo '<tr class="'.$col[$i++ % 2].'"><td>';
	if (strtolower($v['isExpert']) == 'y') { echo '<b><font color="green">'; }
	echo $v['LastName'].', '.$v['FirstName'];
	if (strtolower($v['isExpert']) == 'y') { echo '</font></b>'; }
	echo '</td><td>'.$v['InstitutionName'].'</td><td>'.$v['EmailAddress'].'</td><td>'.$v['Phone'].'</td>';
	if (!empty($v['past_shiftstaken'])) {
	    echo '<td>SUM: <font color="red"><b>'.array_sum($v['past_shiftstaken']).'</b></font>, PY0: <b>'.intval($v['past_shiftstaken'][0]).'</b>, PY1: <b>'.intval($v['past_shiftstaken'][1]).'</b>, PY2: <b>'.intval($v['past_shiftstaken'][2]).'</b></td>';
	} else {
	    echo '<td>SUM: <font color="red"><b>0</b></font>, no past entries found..</td>';
	}
	echo '</tr>';
    }
?>
</table><BR>
<?php } ?>


</td></tr>
</table>

