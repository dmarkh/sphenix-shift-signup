<SCRIPT type="text/javascript" src="js/overlib.js"><!-- overLIB (c) Erik Bosrup --></SCRIPT>
<center>
<h2>Shift Sign-up Expert List</h2>
<h3><a href="?do=controlcenter">Return [back] to controls menu</a> OR <a href="?do=listlocalexperts&pdf=1">get [PDF version <img src="img/icons/adobe-reader.png" width="20" height="20" border="0">]</a> of expert list </h3>
<h3><i><small>* denotes a "OR" i.e. call any of the experts. Otherwise, please call in the order it appears at the list</small></i></h3>
<?php

function dump_child_category(&$v, $prevName = '') {
    if (!empty($v['persons'])) {
	$ind = '';
	foreach ($v['persons'] as $k2 => $v2) {
	    if ($v['catOperation'] == 'or') { $ind = '*'; } else { $ind = ($k2+1).'. '; };
	    echo '<tr><td align="left" width="1%">'.$ind.'</td><td width="10%"><small>'.$prevName.':'.$v['catName'].'</small></td>'.
		'<td width="30%">'
		    .'<a style="text-decoration: none;" href="javascript:void(0);" onmouseover="return overlib(\'Credit: '.$v2['ecred'].'\');" onmouseout="return nd();">'
		    .$v2['exFirstName'].' '.$v2['exLastName'].'</a>';
		echo '</td><td align="center">'.$v2['exPhonePrimary'].'&nbsp;</td>';
		if (empty($v2['exPhoneCell']) && empty($v2['exPhoneHome'])) {
		    echo '<td align="right" colspan="2">'.$v2['exEmail'].'&nbsp;</td>';
		} else {
		echo '<td align="center">'.$v2['exPhoneCell'].'&nbsp;</td>'.
		    '<td align="center">'.$v2['exPhoneHome'].'&nbsp;</td>';
		}
		echo '</tr>';
		if (!empty($v2['exDescription'])) {
		    echo '<tr><td colspan="4">&nbsp;</td><td colspan="2" align="right">'.htmlspecialchars($v2['exDescription']).'</td></tr>';
		}
	}
    } else {
	echo '<tr><td width="10%"><small>'.$prevName.':'.$v['catName'].'</small></td><td colspan="5">&nbsp;</td></tr>';
    }
    if ( !empty($v['children'])) {
	foreach($v['children'] as $k2 => $v2) {
	    dump_child_category($v2, $prevName.':'.$v['catName']);
	}
    }    
}

echo '<table style="width: 80%; border-bottom: 1px solid black;">'."\n";
foreach($cat as $k => $v) {
    echo '<tr><td colspan="6" valign="top"><div style="width: 100%; border-bottom: 1px solid black; height: 2px;">&nbsp;</div></td></tr>'."\n";
    echo '<tr><td colspan="3" valign="top" width="40%" rowspan="2"><b>'.$v['catName'].'</b></td>'.
	'<td width="15%" align="center" valign="top" rowspan="2"><font color="gray"><i>Primary Phone</i></font></td>'.
	'<td align="center"><font color="gray"><i>Cell Phone</i></font></td><td align="center"><font color="gray">Home Phone</i></font></td>'.
	'</tr>'."\n";
    echo '<tr><td align="center" colspan="2"><font color="gray"><i>or E-Mail</i></font></td></tr>';
    echo '<tr><td colspan="6" valign="top"><div style="width: 100%; border-bottom: 1px dashed silver; height: 2px;">&nbsp;</div></td></tr>'."\n";
    if (!empty($v['persons'])) {
	foreach ($v['persons'] as $k2 => $v2) {
	    if ($v2['exDisabled'] == 0) {
	    echo '<tr><td align="left" width="1%">';
	    if ($v['catOperation'] == 'or') { echo '*'; } else { echo ($k2+1).'. '; };
	    echo ' </td>';
	    echo '<td>&nbsp;';
	    echo '</td>'.
		'<td width="35%">'
		    .'<a style="text-decoration: none;" href="javascript:void(0);" onmouseover="return overlib(\'Credit: '.$v2['ecred'].'\');" onmouseout="return nd();">'
		    .$v2['exFirstName'].' '.$v2['exLastName'].'</a>';
		echo '</td><td align="center">'.$v2['exPhonePrimary'].'&nbsp;</td>';
		if (empty($v2['exPhoneCell']) && empty($v2['exPhoneHome'])) {
		echo '<td colspan="2" align="right">'.$v2['exEmail'].'&nbsp;</td>';

		} else {
		echo '<td align="center">'.$v2['exPhoneCell'].'&nbsp;</td>'.
		    '<td align="center">'.$v2['exPhoneHome'].'&nbsp;</td>';
		}
		echo '</tr>';
		if (!empty($v2['exDescription'])) {
		    echo '<tr><td colspan="4">&nbsp;</td><td colspan="2" align="right">'.htmlspecialchars($v2['exDescription']).'</td></tr>';
		}
	    }
	}
    }
    if ( !empty($v['children'])) {
	$tmp = false;
	foreach($v['children'] as $k2 => $v2) {
	    if ($tmp) {
    		echo '<tr><td colspan="6" valign="top"><div style="width: 100%; border-bottom: 1px dashed silver; height: 2px;">&nbsp;</div></td></tr>'."\n";
	    } else {
		$tmp = true;
	    }
	    dump_child_category($v2);
	}
    }
}
echo '</table><br><br><br><br>';
?>