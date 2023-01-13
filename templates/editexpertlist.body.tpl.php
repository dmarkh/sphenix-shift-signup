<script>
function remove_expert(e, name) {
    if (confirm('Do you really want to remove expert ' + name + ' from this category?')) {
        var e3 = document.getElementById('expertID');
        e3.value = e;
        document.forms["remove_expert_form"].submit();
    }
}

function toggle_display_mode() {
        document.forms["toggle_category_display_mode_form"].submit();
}

</script>

<center>
<h2>Expert list for category: <?=$cat['catName'] ?></h2>
<h3><a href="?do=listcategories">[back to category list]</a></h3>
<?php if (!empty($message)) { echo '<h3><font color="red">'.$message.'</font></h3>'; } ?>
<p>Expert Display Mode: <SELECT name="catOp" onChange="toggle_display_mode()">
    <OPTION value="and" <?php if ($cat['catOperation'] == 'and') echo 'selected=selected'; ?>>AND</OPTION>
    <OPTION value="or"  <?php if ($cat['catOperation'] == 'or') echo 'selected=selected'; ?>>OR</OPTION>
</SELECT></p>

<?php if (empty($cat['catExperts'])) { ?>
Expert list is empty, please assign Experts now.
<?php } else { $ex = explode(',', $cat['catExperts']); ?>
<table  id="st_shifts_tbl" style="width: 80%; border-left: 1px solid silver;">
<tr  class="st_shifts_tbl_hdr"><td>Name</td><td>Expertise - Phonebook</td><td>Email</td><td>operation</td></tr>
<?php foreach($ex as $k => $v) { ?>
<tr>
    <td><?=$experts[$v]['exLastName'] ?>, <?=$experts[$v]['exFirstName'] ?></td>
    <td><?=$experts[$v]['exComment'] ?></td>
    <td><?=$experts[$v]['exEmail'] ?></td>
    <td align="center"><input type="button" name="removeexpert" value="REMOVE" onClick="remove_expert(<?php echo $experts[$v]['id'].',\''.$experts[$v]['exFirstName'].' '.$experts[$v]['exLastName'].'\'';?>);"></td>
</tr>
<?php } ?>
</table>

<?php } ?> 
<p>
<form method="post" action="" name="add_expert_form">
<select name="expertID">
<?php foreach($experts as $k => $v) { if ($v['exDisabled'] == 0) { ?>
<option value="<?=$v['id'] ?>"><?=$v['exLastName'] ?>, <?=$v['exFirstName'] ?> | <?=$v['exComment'] ?></option>
<?php }} ?>
</select>
<input type="submit" name="submit" value="ADD EXPERT TO THIS CATEGORY">
<input type="hidden" name="do" value="addexpert">
<input type="hidden" name="catID" value="<?=$cat['id'] ?>">
</form>
</p>

<form method="post" action="" name="remove_expert_form">
<input type="hidden" name="do" value="removeexpert">
<input type="hidden" id="catID" name="catID" value="<?=$cat['id'] ?>">
<input type="hidden" id="expertID" name="expertID" value="">
</form> 

<form method="post" action="" name="toggle_category_display_mode_form">
<input type="hidden" name="do" value="togglecategory">
<input type="hidden" id="catID" name="catID" value="<?=$cat['id'] ?>">
<input type="hidden" id="catID" name="newVal" value="<?php if ($cat['catOperation'] == 'or') { echo 'and'; } else { echo 'or'; } ?>">
</form> 
