<center>
<h2>PHONEBOOK List of Shift Sign-up Experts</h2>
<h3><a href="?do=controlcenter">[back to controls menu]</a></h3>
<table id="st_shifts_tbl" style="width: 80%; border-left: 1px solid silver;">
<tr class="st_shifts_tbl_hdr"><td>Name</td><td>Country</td><td>Institution</td><td>Expertise</td><td>Phone</td><td>Cell Phone</td><td>BNL Phone</td><td>E-Mail</td></tr>

<?php $i = 1; foreach($experts as $k => $v) { $i++; ?>
<tr class="<?php if ($i%2) { echo 'odd'; } else { echo 'even'; } ?>">
    <td><nobr><b><?=$v['LastName'] ?>, <?=$v['FirstName'] ?></b></nobr></td>
    <td align="center">
    <?php
    if (!empty($v['Country'])) {
        if (!empty($v['Country'])) {
	    echo '<img src="img/flags_iso/24x24/'.strtolower($v['Country']).'.png"> ';
        }
    }
    ?></td>
    <td align="left"><nobr><small><?=$v['InstitutionName'] ?></small></nobr></td>
    <td align="center"><?=$v['Expertise'] ?></td>
    <td align="center"><?=$v['Phone'] ?></td>
    <td align="center"><?=$v['CellPhone'] ?></td>
    <td align="center"><?=$v['BnlPhone'] ?></td>
    <td align="center"><a href="mailto:<?=$v['EmailAddress'] ?>"><?=$v['EmailAddress'] ?></a></td>
</tr>
<?php } ?>
</table>

<br><br><br>