<center>
<h2>Manage LOCAL List of Shift Sign-up Experts</h2>
<h3><a href="?do=controlcenter">[back to controls menu]</a> :: <a href="?do=listcategories">[Categories can be managed here]</a></h3>
<?php 
if (empty($experts) || !is_array($experts)) {
?>
<h2>No experts found in local database :(</h2>
<input type="button" name="import" value="IMPORT EXPERTS FROM PHONEBOOK" onClick="document.location='index.php?do=importexperts'">
<?php } else { ?>
<form method="post" action="">
<?php if (!empty($message)) { ?>
<h3><font color="red"><?=$message ?> <?php echo '<br>'.date('r'); ?></font></h3>
<?php } ?>
<p><input type="submit" name="submit" value="UPDATE"> &nbsp;&nbsp;&nbsp;&nbsp; <input type="submit" name="sync" value="IMPORT FROM PHONEBOOK"></p>
<table id="st_shifts_tbl" style="width: 80%; border-left: 1px solid silver;">
<tr class="st_shifts_tbl_hdr"><td>Name</td><td style="background-color: gold;">Credit</td><td>Expertise</td><td>Primary Phone</td><td style="background-color: gold;">Phone</td><td style="background-color: gold;">Cell Phone</td><td style="background-color: gold;">BNL Phone</td><td>Home Phone</td><td style="background-color: gold;">E-Mail</td><td>Additional description</td><td>Op.</td></tr>
<?php 
$i = 1; foreach($experts as $k => $v) { $i++; ?>
<tr class="<?php 
	if ($v['exDisabled'] != 1) {
	    if ($i%2) { echo 'odd'; } else { echo 'even'; }; 
	} else {
	 echo 'expert_disabled';  
	}
    ?>">
    <td>
	<nobr><b><?=$v['exLastName'] ?>, <?=$v['exFirstName'] ?></b></nobr>
    </td>
    <td align="center">
	<!-- <INPUT type="text" name="ecred[<?=$v['id'] ?>]" value="<?=$v['ecred'] ?>" style="width: 40px; text-align: center;"> //-->
	<?php
	if (!empty($v['ecred'])) {
	    echo $v['ecred'];
	} else {
	    echo '0';
	}
	 ?>
    </td>
    <td align="center"><?=$v['exComment'] ?></td>
    <td align="center"><input type="text" name="exPhonePrimary[<?=$v['id'] ?>]" size=10 value="<?=$v['exPhonePrimary'] ?>" <?php if ($v['exDisabled']) echo 'disabled=disabled' ?>></td>
    <td align="center"><input type="text" name="exPhone[<?=$v['id'] ?>]" size=10 value="<?=$v['exPhone'] ?>" <?php if ($v['exDisabled']) echo 'disabled=disabled' ?>></td>
    <td align="center"><input type="text" name="exPhoneCell[<?=$v['id'] ?>]" size=10 value="<?=$v['exPhoneCell'] ?>" <?php if ($v['exDisabled']) echo 'disabled=disabled' ?>></td>
    <td align="center"><input type="text" name="exPhoneBnl[<?=$v['id'] ?>]" size=10 value="<?=$v['exPhoneBnl'] ?>" <?php if ($v['exDisabled']) echo 'disabled=disabled' ?>></td>
    <td align="center"><input type="text" name="exPhoneHome[<?=$v['id'] ?>]" size=10 value="<?=$v['exPhoneHome'] ?>" <?php if ($v['exDisabled']) echo 'disabled=disabled' ?>></td>
    <td align="center"><input type="text" name="exEmail[<?=$v['id'] ?>]" value="<?=$v['exEmail'] ?>" <?php if ($v['exDisabled']) echo 'disabled=disabled' ?>>
    <input type="hidden" name="exID[<?=$v['id'] ?>]" value="<?=$v['exID'] ?>">
    <input type="hidden" name="exFirstName[<?=$v['id'] ?>]" value="<?=$v['exFirstName'] ?>">
    <input type="hidden" name="exLastName[<?=$v['id'] ?>]" value="<?=$v['exLastName'] ?>">
    </td>
    <td align="center"><input type="text" name="exDescription[<?=$v['id'] ?>]" size=20 value='<? echo htmlspecialchars($v['exDescription']); ?>' <?php if ($v['exDisabled']) echo 'disabled=disabled' ?>></td>
    <td align="center">
    <SELECT name="exOp[<?=$v['id'] ?>]">
	<OPTION value="noop">-------</OPTION>
	<?php if ($v['exDisabled']) { ?>
	<OPTION value="restore">RESTORE</OPTION>
	<OPTION value="delete">DELETE</OPTION>
	<?php } else { ?> 
	<OPTION value="disable">DISABLE</OPTION>
	<OPTION value="delete">DELETE</OPTION>
	<?php } ?>
    </SELECT>
    </td>
</tr>
<?php } ?>
</table>
<p><input type="submit" name="submit" value="UPDATE"></p>
<input type="hidden" name="do" value="updateexperts">

<?php } ?>
<br><br><br>