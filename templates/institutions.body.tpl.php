<DIV id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
<SCRIPT language="javascript" type="text/javascript">
function change(url) {
    document.location.href = url;
}
</SCRIPT>
<SCRIPT type="text/javascript" src="js/overlib.js"><!-- overLIB (c) Erik Bosrup --></SCRIPT>
<center>
<p><b>Fast Access : </b> 
<SELECT onChange="change(this.value);">
    <OPTION value="#" selected="selected">CHOOSE INSTITUTION</OPTION>
    <?php 
    foreach($inst as $k => $v) {
	if ( empty($v['name']) ) { continue; }
	echo '<OPTION value="#inst_id_'.$k.'">'.$v['name'].'</OPTION>';
    }
    ?> 
</SELECT></p>

<p><a href="#Stat">Statistics</a> :: <a href="?do=listexperts">Experts</a></p>
    
<table border="1" class="sortable">
<th>
<a href="javascript:void(0);" onmouseover="return overlib('List of Active Institutions.');" onmouseout="return nd();">Institution&nbsp;Name</a>
</th><th>
<a href="javascript:void(0);" onmouseover="return overlib('Total number of authors (active and inactive) active members (non-authors).');" onmouseout="return nd();">#&nbsp;People</a>
</th><th>
<a href="javascript:void(0);" onmouseover="return overlib('Current number of active authors. In the member names listing, author are in blue, inactive authors are in Italic.');" onmouseout="return nd();">Current #&nbsp;of&nbsp;active Authors</a>
</th><th>
<a href="javascript:void(0);" onmouseover="return overlib('Number of authors considered by the Shift committee at the time of the shift duty accounting calculation. This number is the number of active authors at the time shift duties are calculated (therefore fixed for the run) and may not match the current number of authors (dynamic). The dues will be based on this number minus the epxert credit(s) (if any).');" onmouseout="return nd();">Effective #&nbsp;of&nbsp;Authors</a>
</th><th>
<a href="javascript:void(0);" onmouseover="return overlib('Sum of the Expert Credits. Typically, experts will be credited against effective authors to calculate the shift owed. Formula depends on the Shift committee.');" onmouseout="return nd();">#&nbsp;Expert Credits</a>
</th><th>
<a href="javascript:void(0);" onmouseover="return overlib('Number of shifts taken (as per extracted from the shift-signup information).');" onmouseout="return nd();">#&nbsp;Shifts Taken</a>
</th><th>
<a href="javascript:void(0);" onmouseover="return overlib('Shift committee provided number of shift owed by each institutions (as sent to the council). All credits for experts and past dues/bonus are included.');" onmouseout="return nd();">#&nbsp;Shifts Owed</a>
</th><th>
<a href="javascript:void(0);" onmouseover="return overlib('Status is a count of owed minus taken. You need to have at least owed shift taken to be OK.');" onmouseout="return nd();">Status</a>
</th><th>
<a href="javascript:void(0);" onmouseover="return overlib('A full list of members (active and inactive authors and other members) color and style coded for convenience. Authors are in blue, non-authors in default color. Inactive authors (authors who have left the group) are in italic, experts are bold and Emeritus undelined');" onmouseout="return nd();">Member Names</a>
</th>

<?php
$shifts_required_final = 0;
    foreach ($inst as $k => $v) {
    if ( !empty($v['name']) ) {
?>
<TR>
<TD width="30%">&nbsp;<?=$v['name'] ?></TD>
<TD align="center">&nbsp;<?php echo (count($v['active'])+count($v['passive'])+count($v['experts_nonauth'])+count($v['notauthor'])+count($v['experts'])); ?></TD>
<TD align="center">&nbsp;<?php echo (count($v['active'])+count($v['experts'])); ?></TD>
<TD align="center">&nbsp;<?php echo intval($v['effective_authors']); ?></TD>
<!-- <TD align="center">&nbsp;<?php echo (count($v['experts']) + count($v['experts_nonauth'])); ?></TD> //-->
<TD align="center">&nbsp;<?php echo intval($v['detector_experts']); ?></TD>

<TD align="center">&nbsp;<?php echo intval($v['staken']); ?></TD>
<TD align="center">&nbsp;<?php echo intval($v['shifts_required']); ?></TD>
<?php	$shifts_required_final += $v['shifts_required']; ?>
<TD align="center"
<?php $diff = intval($v['shifts_required']) - intval($v['staken']); 
	    if (intval($v['shifts_required']) > 0) { 
		$perc = intval(round(-(intval($v['shifts_required']) - intval($v['staken']))/(intval($v['shifts_required']))*100));
	    } else {
		$perc = 0;
	    }
?>
 sorttable_customkey="<?php echo ($perc-($diff/100)); ?>">&nbsp;

<?php 	    if ($diff <= 0) {
		echo '<span class="gb">';
		if ($perc > 0) { 
		    echo '&#43;'; 
		    echo $perc.'% (OK)'; 
		} else if ($perc == 0) {
		    echo 'Exact coverage (OK)';
		}
		echo '</span>';
	    } else {
		echo '<span class="rb">';
		echo $perc.'% <br />('.$diff.' more needed)</span>';
	    }
?>
</TD>
<TD><a name="inst_id_<?=$k ?>"></a>
<?php	$auth = array();
	if (!empty($v['experts'])) {
	    $auth[] = '<font color="blue">'.implode(', ',$v['experts']).'</font>';
	}
	if (!empty($v['active'])) {
	    $auth[] = '<font color="blue">'.implode(', ',$v['active']).'</font>';
	}
	if (!empty($v['passive'])) {
	    $auth[] = '<i><font color="blue">'.implode(', ',$v['passive']).'</font></i>';
	}
	if (!empty($v['experts_nonauth'])) {
	    $auth[] = '<b>'.implode(', ', $v['experts_nonauth']).'</b>';
	}
	if (!empty($v['notauthor'])) {
	    $auth[] = implode(', ', $v['notauthor']);
	}
	echo implode(', ', $auth);
?>
</TD>
</TR>
<?php  } } ?>

</table></center>

<A NAME="Bot">&nbsp;</A>

<?php
    if ( $shifts_available != 0 ){
      $rat1 = round(($shifts_available - $shifts_required)/$shifts_available*100) . '%';
      $rat2 = round(($shifts_available - $shifts_taken)/$shifts_available*100) . '%';
    } else {
      $rat1 = "undefined";
      $rat2 = "undefined";
    }
    $weeks_equiv = round($shifts_available/$slots_per_week);
?>
<DIV style="background-color: white; border: 1px solid silver; padding: 20px;">
<A NAME="Stat"></A>
<h1><img src="img/icons/institutions.png" border=0 alt="statistical info"> Statistical information</h1>
<p><I>Note:</I> Information is based on counting from the real db (not the test db)</p>

<TABLE border="0" class="none">
<TR class="even">
    <td>Slots Available (TSA)</td><td><?=$shifts_available ?></td>
<td>This corresponds to a <?=$weeks_equiv ?> full weeks equivalent assignment for <?=$total_weeks ?> partial weeks (<?php echo round($weeks_equiv / $total_weeks * 100); ?>% usable).<br>We have: <?=$shifts_trainee_max ?> maximum trainee shifts, <?=$shifts_non_trainee ?> non trainee shifts (<?=$shifts_qa ?> QA shifts included)</td>
</TR>
<TR class="odd">
    <td>Shifts Taken (TST)   </td><td><?=$shifts_taken ?></td>
    <td>Current Shift left un-assigned (TSA-TST)/TSA = <?=$rat2 ?></td>
</TR>
<?php #echo 'DEBUG Counting '.$shifts_required_final. ' Blind sum '.$shifts_required; ?>
<TR class="even">
    <td>Shifts Owed (TSO)    </td><td><?=$shifts_required ?></td>
    <td>
<?php
    if ( $shifts_required > $shifts_available){
      echo 'Shift owed exceeds number of available slots';
    }
?>
    &nbsp;
    </td>
</TR>
<TR class="odd">
    <td>Offset owed versus total, (TSA-TSO)/TSA </td>
    <td><?=$rat1 ?></td>
    <td>
<?php 
    if ($rat1 < 0) {
?>
    Manual allocation over-commited slots or TSA ill-defined
<?php
    }
    $trainee_weeks_occupancy = round($shifts_taken_trainee / $shifts_trainee_max * 100.0, 2);
?>  
    &nbsp;
    </td>
</TR>  
<TR class="even">
    <td>Trainee shifts taken: </td>
    <td><?=$shifts_taken_trainee ?> ( <?=$trainee_weeks_occupancy ?>% occupied )</td>
    <td> Total: <?=$shifts_trainee_max ?></td>
</TR>
</TABLE>
</DIV>
