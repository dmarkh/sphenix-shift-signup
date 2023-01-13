<center>
<h2>ShiftSignup Action Logs</h2>
<h3><a href="?do=controlcenter">[back to controls menu]</a></h3>
<table id="dyntbl" class="display">
<thead>
<tr>
    <th>EVENT TIMESTAMP</th>
    <th>TYPE</th>
    <th>WEEK/SLOT</th>
    <th>SLOT</th>
    <th>SHIFT USER</th>
    <th style="background-color: gold;">IP</th>
    <th style="background-color: gold;">HOST</th>
    <th style="background-color: gold;">AGENT</th>
</tr>
</thead>
<tbody>
<?php $i = 1; if (!empty($listlogs) && is_array($listlogs)) { foreach($listlogs as $k => $v) { $i++; ?>
<tr align="center">
    <td><nobr><b><small>
    <?php echo '<span title="'.$v['act_timestamp'].'"></span>'; ?>
    <?php echo date('r', $v['act_timestamp']); ?>
    </small></b></nobr></td>
    <td>
	<?php 
	    $out = strtoupper($v['action_type']);
	    switch($out) {
		case 'ADDED':
		    echo '&nbsp;&nbsp;<font color="green">'.$out.'</font>&nbsp;&nbsp;';
		    break;
		case 'REMOVED':
		    echo '&nbsp;&nbsp;<font color="red">'.$out.'</font>&nbsp;&nbsp;';
		    break;
		default:
		    echo '&nbsp;&nbsp;'.$out.'&nbsp;&nbsp;';
		    break;
	    }
	?>
    </td>
    <td><nobr><?=$v['week_name'] ?></nobr><br><nobr><?=$v['shift_slot'] ?></nobr></td>
    <td><nobr><?=$v['shift_type'] ?></nobr></td>
    <td><nobr><?=$v['user_name'] ?></nobr><br><nobr><small><?=$v['inst_name'] ?></small></nobr></td>
    <td><small><?=$v['origin_ip'] ?></small></td>
    <td><small><?=$v['origin_host'] ?></small></td>
    <td><small><?=$v['origin_agent'] ?></small></td>
</tr>
<?php } } ?>
</tbody>
</table>

<br><br><br>