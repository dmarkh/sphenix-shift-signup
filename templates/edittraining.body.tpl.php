<center>
<?php if (!empty($message)) { ?>
<h2><font color="red"><?=$message ?></font></h2>
<?php } ?> 
<h3>Editing Training Records for : <b><?=$mem['FirstName'] ?> <?=$mem['LastName'] ?></b> ( <?=$mem['InstitutionName'] ?> )</h3>
<form name="trnupdate" action="" method="post" onSubmit="return window.confirm('Do you really want to update training record for <?=$mem['FirstName'] ?> <?=$mem['LastName'] ?>?')">
<table border=0 style="border: 1px solid gray;">
<tr class="st_shifts_tbl_hdr">
    <td>Training&nbsp;Name</td>
    <td>Training&nbsp;Complete?</td>
    <td>Training&nbsp;Validity&nbsp;Date</td>
    <td>Is&nbsp;pending?</td>
    <td>Comments</td>
    <td>Status</td>
</tr>
<tr class="st_shifts_tbl_row even">
    <td align="center"><b>Period Coordinator</b></td>
    <td align="center">&nbsp;<input type="checkbox" name="trainingComplete[1]" value="1" <?php if (isset($train[1])) { echo 'checked="yes"'; } ?> <?php if (isset($lock[1])) echo "disabled=disabled"; ?>></td>
    <td><input type="text" id="bt1" placeholder="YYYY-MM-DD" name="beginTime[1]" value="<?php if (isset($train[1])) { echo $train[1]['beginTime']; } ?>" class="edit_status_date" maxlength="19" <?php if (isset($lock[1])) echo "disabled=disabled"; ?>></td>
    <td align="center">
	<select name="isPending[1]" <?php if (isset($lock[1])) echo "disabled=disabled"; ?>>
	    <option value="0" <?php if (isset($train[1]) && $train[1]['isPending'] == 0) { echo 'selected=selected'; } ?>>No</option>
	    <option value="1" <?php if (isset($train[1]) && $train[1]['isPending'] == 1) { echo 'selected=selected'; } ?>>Yes</option>
	</select>
    </td>
    <td><input type="text" id="com1" name="comments[1]" value="<?php echo $train[1]['comments']; ?>" maxlength="19" /></td>
    <td>&nbsp;<?php if (isset($lock[1])) echo "User has active shifts of this type, cannot deactivate this training record"; ?>
    &nbsp;<input type="hidden" name="locked[1]" value="<?php if (isset($lock[1])) { echo "1"; } else { echo "0"; } ?>"></td>
</tr>
<tr class="st_shifts_tbl_row odd">
    <td align="center"><b>Shift Leader</b></td>
    <td align="center">&nbsp;<input type="checkbox" name="trainingComplete[2]" value="1" <?php if (isset($train[2])) { echo 'checked="yes"'; } ?> <?php if (isset($lock[2])) echo "disabled=disabled"; ?>></td>
    <td><input type="text" id="bt2" placeholder="YYYY-MM-DD" name="beginTime[2]" value="<?php if (isset($train[2])) { echo $train[2]['beginTime']; } ?>" class="edit_status_date" maxlength="19" <?php if (isset($lock[2])) echo "disabled=disabled"; ?>></td>
    <td align="center">
	<select name="isPending[2]" <?php if (isset($lock[2])) echo "disabled=disabled"; ?>>
	    <option value="0" <?php if (isset($train[2]) && $train[2]['isPending'] == 0) { echo 'selected=selected'; } ?>>No</option>
	    <option value="1" <?php if (isset($train[2]) && $train[2]['isPending'] == 1) { echo 'selected=selected'; } ?>>Yes</option>
	</select>
    </td>
    <td><input type="text" id="com2" name="comments[2]" value="<?php echo $train[2]['comments']; ?>" maxlength="19" /></td>
    <td>&nbsp;<?php if (isset($lock[2])) echo "User has active shifts of this type, cannot deactivate this training record"; ?>
    &nbsp;<input type="hidden" name="locked[2]" value="<?php if (isset($lock[2])) { echo "1"; } else { echo "0"; } ?>"></td>
</tr>

<tr class="st_shifts_tbl_row even">
    <td align="center"><b>Detector Operator</b></td>
    <td align="center">
    &nbsp;<input type="checkbox" name="trainingComplete[3]" value="1" <?php if (isset($train[3])) { echo 'checked="yes"'; } ?> <?php if (isset($lock[3])) echo "disabled=disabled"; ?>>
    </td>
    <td><input type="text" id="bt3" placeholder="YYYY-MM-DD" name="beginTime[3]" value="<?php if (isset($train[3])) { echo $train[3]['beginTime']; } ?>" class="edit_status_date" maxlength="19" <?php if (isset($lock[3])) echo "disabled=disabled"; ?>></td>
    <td align="center">
	<select name="isPending[3]" <?php if (isset($lock[3])) echo "disabled=disabled"; ?>>
	    <option value="0" <?php if (isset($train[3]) && $train[3]['isPending'] == 0) { echo 'selected=selected'; } ?>>No</option>
	    <option value="1" <?php if (isset($train[3]) && $train[3]['isPending'] == 1) { echo 'selected=selected'; } ?>>Yes</option>
	</select>
    </td>
    <td><input type="text" id="com3" name="comments[3]" value="<?php echo $train[3]['comments']; ?>" maxlength="19" /></td>
    <td>&nbsp;<?php if (isset($lock[3])) echo "User has active shifts of this type, cannot deactivate this training record"; ?>
	&nbsp;<input type="hidden" name="locked[3]" value="<?php if (isset($lock[3])) { echo "1"; } else { echo "0"; } ?>"></td>
</tr>

<tr class="st_shifts_tbl_row odd">
    <td align="center" width="15%"><b>Offline QA</b></td>
    <td align="center" width="5%">
    &nbsp;<input type="checkbox" name="trainingComplete[4]" value="1" <?php if (isset($train[4])) { echo 'checked="yes"'; } ?>>
    </td>
    <td width="15%"><input type="text" placeholder="YYYY-MM-DD" id="bt4" name="beginTime[4]" value="<?php if (isset($train[4])) { echo $train[4]['beginTime']; } ?>" class="edit_status_date" maxlength="19"></td>
    <td align="center" width="5%">
	<select name="isPending[4]">
	    <option value="0" <?php if (isset($train[4]) && $train[4]['isPending'] == 0) { echo 'selected=selected'; } ?>>No</option>
	    <option value="1" <?php if (isset($train[4]) && $train[4]['isPending'] == 1) { echo 'selected=selected'; } ?>>Yes</option>
	</select>
    </td>
    <td width="15%"><input type="text" id="com4" name="comments[4]" value="<?php echo $train[4]['comments']; ?>" maxlength="19" /></td>
    <td width="5%"><?php if (isset($lock[4])) { echo "User has active shifts of this type"; } ?></td>
</tr>

</table>
<input type="hidden" name="do" value="updatetraining">
<input type="hidden" name="id" value="<?=$mem['Id'] ?>">
<p>
    <input type="submit" value="UPDATE TRAINING RECORDS">
</p>
<p style="color: #F00; font-weight: bold;">When NEW training is added manually, please check the "Training Complete?" box too. When training needs to be removed, simply uncheck "Training Complete?" box.</p>
<p>If this person has already signed for e.g. Det.Op shifts, you cannot disable his Det.Op. training. Please unsubscribe from such slot first.</p>
<p>
<?php 
if (!empty($lock[1]) || !empty($lock[2]) || !empty($lock[3])) {
?>
<SCRIPT>
function unlock_fields() {
    var test = confirm("Are you ABSOLUTELY SURE that you need to unlock protected fields?");
    if (test) {
	document.forms[0].elements["locked[1]"].value = 0;
	document.forms[0].elements["locked[2]"].value = 0;
	document.forms[0].elements["locked[3]"].value = 0;
	document.forms[0].elements["beginTime[1]"].disabled = false;
	document.forms[0].elements["beginTime[2]"].disabled = false;
	document.forms[0].elements["beginTime[3]"].disabled = false;
	document.forms[0].elements["trainingComplete[1]"].disabled = false;
	document.forms[0].elements["trainingComplete[2]"].disabled = false;
	document.forms[0].elements["trainingComplete[3]"].disabled = false;
	document.forms[0].elements["isPending[1]"].disabled = false;
	document.forms[0].elements["isPending[2]"].disabled = false;
	document.forms[0].elements["isPending[3]"].disabled = false;
    }
}
</SCRIPT>
<INPUT type="button" name="unlock" value="Emergency Bypass: ignore protection mechanism and unlock protected fields" onClick="unlock_fields();">
<?php } ?>
</p>
</form>
</center>



