<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
<script type="text/javascript" src="js/overlib.js"><!-- overLIB (c) Erik Bosrup --></script>
<SCRIPT language="javascript" type="text/javascript">
function change(url) {
    document.location.href = url;
}

function check_reinit() {
  if (confirm("Are you sure you want to reinitialize effective authors?")) {
    document.getElementById("hidden_action").value = 'updateaccounting_reinit';
    document.getElementById("accounting_form").submit();
  }
  return false;
}

</SCRIPT>

    <center><h2><img src="img/icons/pref.png" border="0" alt="preferences"> Shiftsignup Edit Menu</h2>
    <?php if (!empty($message)) { echo '<h2><font color="red">'.$message.'</font></h2>'; } ?>
    <p><b>Fast Access : </b> <SELECT onChange="change(this.value);">
    <OPTION value="#" selected="selected">CHOOSE INSTITUTION</OPTION>
    <?php
        foreach($inst as $k => $v) {
	    echo '<OPTION value="#inst_id_'.$k.'">'.$v['name'].'</OPTION>';
	}
    ?>
    </SELECT></p>
			
    <FORM action="" method="post" id="accounting_form">
    <p>
    <center>
    <INPUT class="fm" type="submit" name="send" value="Save" style="margin-right: 25px;">
    OR
    <INPUT type="text" name="scale" value="<?=$scale ?>" style="margin-left: 25px;" placeholder="scale factor like 1.0 or 1.5">
    <INPUT class="fm" type="submit" name="send" value="Scale and Save" style="margin-right: 25px;"> or, alternatively, do 
    <INPUT class="fm" type="button" name="init_effective_authors" value="Reinitialize effective authors" onClick="check_reinit();" style="margin-left: 25px;">

    <?php if (!empty($old_scale)) { echo '<font color="gray">Scale, used for last update: '.$old_scale.'</font>'; } ?>

    <table border="1" class="sortable">
    <th>
    <a href="javascript:void(0);" onmouseover="return overlib('List of Active Institutions. Institutions having no council representative are displayed in <i>italic font</i>');" onmouseout="return nd();">Institution&nbsp;Name</a>
    </th><th>
    <a href="javascript:void(0);" onmouseover="return overlib('Current number of active authors.');" onmouseout="return nd();">Current&nbsp#&nbsp; of active Authors</a>
    </th><th>
    <a href="javascript:void(0);" onmouseover="return overlib('Number of authors considered by the Shift committee at the time of the shift duty accounting calculation. This number is the number of active authors at the time shift duties are calculated (therefore fixed for the run) and may not match the current number of authors (dynamic). The dues will be based on this number minus the epxert credit(s) (if any).');" onmouseout="return nd();">Effective&nbsp;#&nbsp;Authors</a>
    </th><th>
    <a href="javascript:void(0);" onmouseover="return overlib('Sum of the Expert Credits. Typically, experts will be credited against effective authors to calculate the shift owed. Formula depends on the Shift committee.');" onmouseout="return nd();">#&nbsp;Expert Credits</a>
    </th><th>
    <a href="javascript:void(0);" onmouseover="return overlib('Number of Authors minus the experts. This number is used to calculate the shift dues');" onmouseout="return nd();">&nbsp;Adjusted # of Authors</a>
    </th><th>
    <a href="javascript:void(0);" onmouseover="return overlib('Shift committee provided number of shift owed by each institutions (as sent to the council). All credits for experts and past dues/bonus are included.');" onmouseout="return nd();">#&nbsp;Shifts Owed</a>
    </th><th>
    <a href="javascript:void(0);" onmouseover="return overlib('Shift committee provided number of extra shifts requested by institution');" onmouseout="return nd();">#&nbsp;Shifts Extra</a>
    </th><th>
    <a href="javascript:void(0);" onmouseover="return overlib('Historical data in a form of shifts_owed:shifts_taken pairs, divided by ";"');" onmouseout="return nd();">#&nbsp;Historical Data</a>
    </th>
    
    <?php foreach ($inst as $k => $v) { ?>
        <TR>
        <TD width="30%"><a name="inst_id_<?=$k ?>">&nbsp;
	    <?php if (empty($v['rep_id'])) { echo '<i>'.$v['name'].'</i>'; } else { echo $v['name']; } ?>
	</a></TD>
        <TD align="center" width="10%">&nbsp;<?=$v['nauth'] ?></TD>
        <TD align="center"><input class="ed" type="text" name="effective_authors[<?=$k ?>]" value="<?php echo intval($v['defaults']['effective_authors']); ?>"></TD>
        <TD align="center">
	&nbsp;<b><?=$v['nexperts'] ?></b>
	</TD>
	<TD align="center" width="10%">&nbsp;<b>
	    <?php 
		$adj = (intval($v['defaults']['effective_authors']) - intval($v['nexperts']));
		if ($adj < 0) { $adj = 0; }
		echo $adj; 
	    ?></b></TD>
        <TD align="center"><input class="ed" type="text" name="shifts_required[<?=$k ?>]" value="<?php echo intval($v['defaults']['shifts_required']); ?>"></TD>
        <TD align="center"><input class="ed" type="text" name="shifts_extra[<?=$k ?>]" value="<?php echo intval($v['defaults']['shifts_extra']); ?>"></TD>
        <TD align="center"><input class="ed" type="text" name="historical_data[<?=$k ?>]" value="<?php echo $v['defaults']['historical_data']; ?>"></TD>
        </TR>
    <?php } ?>
    </table>
    <br>
    <INPUT class="fm" type="submit" name="send" value="Save" style="margin-right: 50px;">
    <input type="hidden" name="do" value="updateaccounting" id="hidden_action">
    </FORM></center>

    <a name="summary">&nbsp;</a>
    <DIV style="background-color: white; border: 1px solid silver; padding-bottom: 20px; padding-left: 20px; padding-right: 20px; width: 40%;">
    <h2><img src="img/icons/tasklist.png" border="0" alt="summary"> Summary</h2>
    <table style="border: 1px solid silver;" >
    <tr class="odd"><td align="left" width="50%">The total number of possible shifters is </td><td><b><?=$totalP ?></b> people</td></tr>
    <tr class="even"><td align="left" width="50%">The total number of shift dues as entered </td><td><b><?=$totalS ?></b> shifts</td></tr>
    <tr class="odd"><td align="left" width="50%">The total number non-training shift slots is </td><td><b><?=$non_trainee ?></b> shifts</td></tr>
    </table>
    </DIV>
    <br/>
    <br/>
    
    