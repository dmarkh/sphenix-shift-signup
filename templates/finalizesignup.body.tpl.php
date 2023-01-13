<center>
<h3>Thank you, <font color="blue"><?=$personName ?></font>. Your shift signup request has been processed:</h3>

<table align="center" border=1 cellpadding=2> 
<?php if (!empty($message)) { echo '<tr><td colspan="4" align="center">'.$message.'</td></tr>'; } ?>
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
<input type="button" value="Return to Shift Signup form" onClick="document.location.href = '?do=shifttable&sel1=i_<?=$institutionID ?>&sel2=<?=$personID ?>'">
</center>
