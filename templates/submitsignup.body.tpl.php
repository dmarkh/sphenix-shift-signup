<center> 
<FORM method="post" action=""> 
<input type="hidden" name="personID" value="<?=$personID ?>">
<input type="hidden" name="personName" value="<?=$personName ?>">
<input type="hidden" name="institutionID" value="<?=$institutionID ?>">
<input type="hidden" name="institutionName" value="<?=$institutionName ?>">
<input type="hidden" name="displayNum" value="<?=$displayNum ?>">
<input type="hidden" name="do" value="finalizesignup">

<?php  
if (!empty($weeks)) { 
    foreach($weeks as $k => $v) {
?>
<input type=hidden name="week[<?=$k ?>]" value="<?=$v['value'] ?>">
<?php }} ?>

<h3>The shifts requested for <font color=blue><?=$personName ?></font> are</h3>

<table align="center" border=1 cellpadding=2> 

<tr bgcolor="yellow" align="center">
    <td width=25%> Week </td>
    <td width=25%> Hours </td>
    <td width=25%> Position </td>
    <td width=25%> Action </td>
</tr>

<?php if (!empty($weeks)) {
    foreach($weeks as $k => $v) { 
?>
<tr align="center">
    <td><?=$v['dates'] ?></td>
    <td><?=$v['times'] ?></td>
    <td><?=$v['shiftType'] ?></td>
    <td><?=$v['operation'] ?></td>
</tr>
<?php }} ?>

<?php if (!empty($warnings)) { ?>
<tr>
    <td colspan="4" align="center">
	<font color="red">
	<?php foreach($warnings as $k => $v) {
	    echo $v.'<BR>';
	} ?>
	</font>
    </td>
</tr>
<?php } ?>

</table>

<hr width="50%"> 
<center>
<input type=submit name="store" value="Store Results"> &nbsp<input type=submit name="cancel" value="Cancel">
</center>
</FORM>
