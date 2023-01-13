<?php if (empty($noid)) { ?>
<center>
<h2><font color="green">All Users for this week and two coming weeks have their BNL IDs registered with ShiftSignup, hooray!</font></h2>

<?php } else { ?>
<center>
<h2><font color="red">Users with unknown BNL IDs for the current and two incoming weeks</font></h2>
<table border=0 cellpadding=3>
<?php foreach($noid as $k => $v) { ?>
<tr style="font-size: 16px;">
    <td><b><?=$v['LastName'] ?>, <?=$v['FirstName'] ?></b></td>
    <td> <i><?=$v['EmailAddress'] ?></i> </td>
    <td> <?=$v['InstitutionName'] ?></td>
    <td> <input type="text" name="bnlid" id="bnlid_<?=$v['Id'] ?>" value="" placeholder="BNL ID">
    <input type="button" class="person_check_button" name="check_user" value="CHECK USER" data-phid="<?=$v['Id'] ?>" data-inid="<?=$v['inst_id'] ?>">
    </td>
    </tr>
<?php } } ?>
</table>
<br>
<input type="button" name="loc_reload" id="loc_reload" value="REFRESH LIST">
