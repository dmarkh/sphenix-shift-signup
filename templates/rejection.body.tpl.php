<DIV id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
<SCRIPT language="javascript" type="text/javascript">
function change(url) {
    document.location.href = url;
}

function display_changes() {
var str="<?php
$ids = array();
if (!empty($punished)) {
echo 'Following names will be removed from author list: ';
foreach($punished as $k => $v) {
    echo $k.') '.$v['inst_name'].' : '.implode(', ', $v['removed']).'; ';
    $ids = array_merge($ids, array_keys($v['removed']));
}
}
?>"
    confirm_changes(str, '<?php echo implode(",", $ids); ?>');
}

function display_delete() {
    var str="Do you really want to delete existing rejection record? ALERT: This transaction cannot be rolled back!";
    var go = confirm(str);
    if (go) {
	window.location = 'index.php?do=deleterejection';
    }
}

function display_notifyreps() {
    var str="Do you really want to send out notification email? ALERT: No further changes will be allowed for this year!";
    var go = confirm(str);
    if (go) {
	window.location = 'index.php?do=notifyrejection';
    }
}

function confirm_changes(str, ids) {
    var params = '&par_I='+document.getElementById('par_I').value + '&par_P='+document.getElementById('par_P').value + '&par_M='+document.getElementById('par_M').value;
    var go = confirm(str);
    if (go) {
	window.location = 'index.php?do=finalizerejection&ids='+ids+params;
    }
}

</SCRIPT>
<SCRIPT type="text/javascript" src="js/overlib.js"><!-- overLIB (c) Erik Bosrup --></SCRIPT>
<center>
<h1>Author Rejection Interface</h1>
<?php if (empty($rej)) { ?>
<p><form method="post" action="">
<fieldset>
<legend>CURRENT PARAMETERS</legend>
<?php  
foreach($par as $k => $v) {
    echo '<b>'.$k.'</b> = <input type="edit" size="3" name="par_'.$k.'" id="par_'.$k.'" value="'.$v.'"> &nbsp;&nbsp;';
}
?><input type="submit" name="submit" value="recalculate">
<BR>
<b>I</b> = number of institutions in top; <b>T</b> = times over a period of; <b>P</b> = period of years; <b>M</b> = allowed percentage of shift dues not taken by collaboration
<input type="hidden" name="do" value="rejection">
</fieldset>
</form>
</p>
<?php } ?>
<?php if (!empty($err)) { ?>
<div style="background-color: red; color: white; font-weight: bold; padding: 10px; border: 1px dashed white;margin-bottom: 10px;">
<?=$err ?>
</div>
<? } ?>
<?php if (!empty($warn)) { ?>
<div style="background-color: yellow; color: black; font-weight: bold; padding: 10px; border: 1px dashed gray;">
<?=$warn ?>
</div>
<? } ?>

<?php
    if (!empty($rej)) {
?>
<div style="background-color: pink; color: brown; font-weight: normal; padding: 0; border: 1px dashed gray;">
<?php 
	echo '<table border=0 cellpadding="5">';
	echo '<tr><th colspan="2" style="background-color: white; color: black;">Recorded Rejection History</th></tr>';
	foreach($rej as $k => $v) {
	    echo '<tr><td align="center"><nobr><b>'.$v['entryTime'].'</b></nobr><br><small>'.$v['ip'].'</small><br><b>'.$v['params'].'</b></td><td>'
		.nl2br(strip_tags($v['authors']))
		.'</td></tr>';
	}
	echo '</table>';
?>
</div>
<? } ?>

<p><fieldset>
<legend>ACCESS PANEL</legend>

<?php if (empty($rej)) { ?>
<input type="button" name="apply" value="STORE REJECTION RECORD" onClick="display_changes()">

<?php } else { ?>
 
<input type="button" name="delete" value="DELETE REJECTION RECORD <?php if (intval($rej[0]['isSent']) != 0) { echo '(EMAIL ALREADY SENT OUT!)'; }; ?>" onClick="display_delete()"> 

&nbsp; 
<?php  if (intval($rej[0]['isSent']) == 0) { ?>
<input type="button" name="notifyreps" value="NOTIFY REPRESENTATIVES BY EMAIL" onClick="display_notifyreps()"> 
<?php } else { ?>
<?php } } ?>

&nbsp; OR &nbsp;
<b>Fast Access : </b> 
<SELECT onChange="change(this.value);">
    <OPTION value="#" selected="selected">CHOOSE INSTITUTION</OPTION>
    <?php 
    foreach($inst as $k => $v) {
	echo '<OPTION value="#inst_id_'.$k.'">'.$v['name'].'</OPTION>';
    }
    ?> 
</SELECT></fieldset></p>
    
<table border="1" class="sortable">
<tr>
<th>
<a href="javascript:void(0);" onmouseover="return overlib('List of Active Institutions.');" onmouseout="return nd();">Institution&nbsp;Name</a>
</th>
<th>
<a href="javascript:void(0);" onmouseover="return overlib('Shift committee provided number of shift owed by each institutions (as sent to the council). All credits for experts and past dues/bonus are included.');" onmouseout="return nd();">#&nbsp;Shifts Dues</a>
</th>
<th>
<a href="javascript:void(0);" onmouseover="return overlib('Number of shifts taken (as per extracted from the shift-signup information).');" onmouseout="return nd();">#&nbsp;Shifts Covered</a>
</th>
<th>
<a href="javascript:void(0);" onmouseover="return overlib('R = shift served / shift owed');" onmouseout="return nd();"><nobr>R ratio</nobr></a>
</th>
<th>
<a href="javascript:void(0);" onmouseover="return overlib('Percentage of non-covered shifts (1-R)%');" onmouseout="return nd();">Missed percentage, current</a>
</th>
<th>
<a href="javascript:void(0);" onmouseover="return overlib('Percentage of non-covered shifts (1-R)%');" onmouseout="return nd();">Missed percentage, historical</a>
</th>
<th>
<a href="javascript:void(0);" onmouseover="return overlib('D Raw');" onmouseout="return nd();"><nobr>D Raw</nobr></a>
</th>
<th>
<a href="javascript:void(0);" onmouseover="return overlib('D Final');" onmouseout="return nd();"><nobr>D Final</nobr></a>
</th>
<th width="30%">
<a href="javascript:void(0);" onmouseover="return overlib('Active authors, including experts');" onmouseout="return nd();">Active Author Names</a>
</th>
<th width="30%">
<a href="javascript:void(0);" onmouseover="return overlib('Randomly selected names for removal');" onmouseout="return nd();">Names to be removed</a>
</th>
</tr>

<?php
    foreach ($inst as $k => $v) {
?>
<TR style="background-color: <?=$v['color'] ?>">
<TD width="20%">&nbsp;<?=$v['name'] ?></TD>
<TD align="center"><?php echo intval($v['shifts_required']); ?></TD>
<TD align="center"><?php echo intval($v['shifts_taken']); ?></TD>
<TD align="center"><?php echo round($v['r'],2); ?></TD>
<TD align="center"><?php echo $v['pct_miss'].'%'; ?></TD>
<!-- // to use first historical entry, shift comments one line up 
<TD align="center"><?php echo intval($v['hist'][0]).'%'; ?></TD>
//-->
<?php
    $hist_values = '';
    if (!empty($v['hist'])) {
	$hist_values .= implode(" :: ", $v['hist']);
//    	foreach($v['hist'] as $k2 => $v2) {
//	  $hist_values .= '// '.$v2.'% ';
//    	}
      //$par['P']
      $vtmp = array_slice($v['hist'], 0, intval($par['P']));
      $vtmp = array_sum($vtmp) / count($vtmp);
      $avg_n_years = round($vtmp,2);
      //echo '<br>avg. over '.$par['P'].' years: <b>'.round($vtmp,2).'%</b>';
    }
?>
<TD align="center" sorttable_customkey="<?=$vtmp ?>"><nobr><?=$hist_values ?><br>avg. over <?=$par['P'] ?> years: <b><?=$avg_n_years ?>%</b></nobr></TD>
<TD align="center"><?php echo $v['D']; ?></TD>
<TD align="center"><?php echo $v['DF']; ?></TD>
<TD><a name="inst_id_<?=$k ?>"></a>
<?php	$auth = array();
	if (!empty($v['experts'])) {
	    $auth[] = '<b><font color="blue">'.implode(', ',$v['experts']).'</font></b>';
	}
	if (!empty($v['active'])) {
	    $auth[] = '<font color="blue">'.implode(', ',$v['active']).'</font>';
	}
	echo implode(', ', $auth);
?>
</TD>
<TD>
<?php 
    if (!empty($v['removed_authors'])) {
	echo '<font color="red">'.implode('<br>', $v['removed_authors']).'</font>';
    }
?>
</TD>
</TR>
<?php  } ?>

</table></center>

</DIV>
