
<table width="100%" height="100%" id="st_hdr_tbl">

<tr>
    <td rowspan="4" id="st_hdr_star"><a href="?do=settings" target="info" style="text-decoration: none;">S</a><br/>P<br/>H<br/>E<br/>N<br/>I<br/>X</td>
    <td rowspan="4" id="st_hdr_sign">S<br/>H<br/>I<br/>F<br/>T<br/>S</td>
    <td rowspan="3" id="st_hdr_run">SPHENIX<br/>Shift&nbsp;Signup<br/><?=$run_name ?></td>
    <td style="padding-left: 30px;"><b>(1) To Signup:</b> First select your Institution and Name, then choose a signup sheet</td>
    <td rowspan="4">
	<table id="st_img_tbl" cellpadding="4" cellmargin="0">
	<tr align="center">
	<td><a target="_blank" href="?do=tablepdf"><img src="img/icons/adobe-reader.png" border="0" alt="PDF format"></a></td>
	<td><a href="?do=institutions" target="info"><img src="img/icons/institutions.png" border="0" alt="Institutions"></a></td>
	<td><a href="?do=graphs" target="info"><img src="img/icons/graph.png" border="0" alt="Graphs"></a></td>
	<td><a href="?do=controlcenter" target="info"><img src="img/icons/pref.png" border="0" alt="Control Center"></a></td>
	</tr>
	<tr align="center">
	<td><a target="_blank" href="?do=tablepdf">PDF</a>::<a target="_blank" href="?do=tablepdfcur">TODAY</a></td>
	<td><a href="?do=institutions" target="info">Institutions</a></td>
	<td><a href="?do=graphs" target="info">Graphs</a></td>
	<td><a href="?do=controlcenter" target="info">Controls</a></td>
	</tr>
	</table>
    </td>
</tr>
<tr>
    <td style="padding-left: 30px;"><b>(2) To view schedules:</b> choose a signup sheet</td>
    <td></td>
</tr>
<tr>
    <td style="padding-left: 30px;">
    <nobr>
    <select name="inst_id" id="inst" class="mod_select" style="width: 500px;">
	<option value="">--- Institutions ---</option>
	<?php foreach ($inst as $k => $v) { ?>
	    <option value="i_<?=$v['id'] ?>" 
	<?php if (isset($sel1) && $sel1 == "i_".$v['id']) { echo "selected=selected"; } ?>
	style="background-image:url(img/flags_iso/24x24/<?php echo strtolower($v['cnt']); ?>.png);"
	><?=$v['name'] ?></option>
	<?php } ?>
    </select>
    <select name="person_id" id="person" style="width: 300px;">
	<option value="">------ People ------</option>
	<?php foreach($memb as $k => $v) { ?>
	<option value="<?=$v['Id'] ?>" <?php if (isset($sel2) && $sel2 == $v['Id']) { echo "selected=selected"; } ?> class="i_<?=$v['InstitutionId'] ?>"><?=$v['LastName'] ?>, <?=$v['FirstName'] ?></option>
	<?php } ?>
    </select>
    </nobr>
    </td>
    <td></td>
</tr>
<tr>
    <td align="center">
    <?php if (stristr($_SERVER['HTTP_USER_AGENT'], 'Gecko') !== false) { ?>
    <a name="shrinkme"><input type="button" name="shrink" value="Compact menu" id="shrink"><input type="button" name="expand" value="Full menu" id="expand" class="start_hidden"></a>
    <?php } ?>
    </td>
    <td style="padding-left: 30px;"><b>Sign-up Sheets:</b>
    &nbsp;&nbsp;&nbsp;&nbsp;<input type="button" name="exp_operations" value="Experiment Operations" id="expops">
    <!-- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" name="offlineqa" value="Offline QA" id="oflqa"> //-->
    &nbsp;&nbsp;&nbsp;&nbsp; OR &nbsp;&nbsp;&nbsp;&nbsp; <input type="button" name="exp_operations_reduced" value="Shift Table: reduced view" id="expops_reduced">
    </td>
</tr>
</table>

<script type="text/javascript">
  function printframe() {
	parent.location = parent.frames[1].location;
  }
  $(document).ready(function() {
    $("#person").chained("#inst");

    $('#oflqa').click(function() {
	var p1 = $('#inst').val();
	if (!p1) { p1 = 0; }
	var p2 = $('#person').val();
	parent.frames[1].location = "index.php?do=offlineqa&sel1="+p1+"&sel2="+p2;
    });
    $('#expops').click(function() {
	var p1 = $('#inst').val();
	if (!p1) { p1 = 0; }
	var p2 = $('#person').val();
	parent.frames[1].location = "index.php?do=shifttable&sel1="+p1+"&sel2="+p2+"#week_<?=$current_week ?>";
    });
    $('#expops_reduced').click(function() {
	var p1 = $('#inst').val();
	if (!p1) { p1 = 0; }
	var p2 = $('#person').val();
	parent.frames[1].location = "index.php?do=shifttable&reduced=1&sel1="+p1+"&sel2="+p2+"#week_<?=$current_week ?>";
    });
    $('#shrink').click(function() {
	$('#shrink').hide();
	$('#expand').show();
	parent.document.body.rows = '30,*';
	parent.frames[0].location = "#shrinkme";
	
    });
    $('#expand').click(function() {
	$('#expand').hide();
	$('#shrink').show();
	parent.document.body.rows = '120,*';
    });
    
    $('#person').change(function() {
	var p1 = $('#inst').val();
	if (!p1) { p1 = 0; }
	var p2 = $('#person').val();
	if (p2 != 0) {
	    parent.frames[1].location = "index.php?do=shiftssigned&sel1="+p1+"&sel2="+p2;	
	}
    });
    <?php if (isset($snowing)) { ?>
    $('body').snowing();
    <?php } ?>
  });

</script>

