<center>
<table class="" border=1>
<tr style="background-color: silver;"><td align="center" colspan="2"><b>RUN</b></td></tr>
<?php $i = 1; foreach($run as $k => $v) { $i++; ?>
<tr class="<?php if ($i%2) { echo 'odd'; } else { echo 'even'; } ?>"><td><b><?=$k ?></b></td>
<td> <?php if (!empty($v)) { echo $v; } else { echo 'no | false'; } ?> </td>
</tr>
<? } ?>
<tr class=""><td colspan=2><hr></td></tr>
<tr style="background-color: silver;"><td align="center" colspan="2"><b>GENERIC</b></td></tr>
<?php foreach($generic as $k => $v) { $i++; ?>
<tr class="<?php if ($i%2) { echo 'odd'; } else { echo 'even'; } ?>"><td><b><?=$k ?></b></td>
    <td> <?php if (!empty($v)) { echo $v; } else { echo 'no | false'; } ?> </td>
</tr>
<? } ?>
<tr class=""><td colspan=2><hr></td></tr>
<tr style="background-color: silver;"><td align="center" colspan="2"><b>DATABASE: MAIN</b></td></tr>
<?php foreach($db as $k => $v) { 
if ($k != 'pass') { $i++;
?>
<tr class="<?php if ($i%2) { echo 'odd'; } else { echo 'even'; } ?>"><td><b><?=$k ?></b></td>
<td> <?php if (!empty($v)) { echo $v; } else { echo 'no | false'; } ?> </td>
</tr>
<? } } ?>
<tr class=""><td colspan=2><hr></td></tr>
<tr style="background-color: silver;"><td align="center" colspan="2"><b>DATABASE: FAKE</b></td></tr>
<?php foreach($db_fake as $k => $v) { 
if ($k != 'pass') { $i++;
?>
<tr class="<?php if ($i%2) { echo 'odd'; } else { echo 'even'; } ?>"><td><b><?=$k ?></b></td>
<td> <?php if (!empty($v)) { echo $v; } else { echo 'no | false'; } ?> </td>
</tr>
<? } } ?>
<tr class=""><td colspan=2><hr></td></tr>
<tr style="background-color: silver;"><td align="center" colspan="2"><b>GRAPHS</b></td></tr>
<?php foreach($graph as $k => $v) { $i++; ?>
<tr class="<?php if ($i%2) { echo 'odd'; } else { echo 'even'; } ?>"><td><b><?=$k ?></b></td>
<td> <?php if (!empty($v)) { echo $v; } else { echo 'no | false'; } ?> </td>
</tr>
<? } ?>
</table>
</center>
<br><br><br>