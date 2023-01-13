<center>
<h2>Shift Sign-up Expert Categories</h2>
<h3><a href="?do=controlcenter">[back to controls menu]</a> :: <a href="?do=manageexperts">[credit attribution should be set here]</a></h3>
<p>Please select expert category and click desired action below.<br> Drag and drop categories to reorder (new position is saved automatically).</p>
<script>

function moveup_cat(e) {
    var e1 = document.getElementById('catAction');
    e1.value='moveup';
    var e2 = document.getElementById('catID');
    e2.value = e;
    document.forms["myform"].submit();	
}

function movedown_cat(e) {
    var e1 = document.getElementById('catAction');
    e1.value='movedown';
    var e2 = document.getElementById('catID');
    e2.value = e;
    document.forms["myform"].submit();	
}

</script>

<table>
<tr align="center">
<td> <input type="button" name="addnew" value="ADD TOP-LEVEL CATEGORY" onClick="add_top_category();"> </td>
<td> <input type="button" name="test" value="DELETE" onClick="delete_category();"> </td>
<td> <input type="button" name="test" value="ADD CHILD" onClick="add_child_category();"> </td>
<td> <input type="button" name="test" value="RENAME" onClick="rename_category();"> </td>
<td> <input type="button" name="test" value="EDIT EXPERT LIST" onClick="edit_expert_list();"> </td>
</tr>
<tr><td colspan="6">
<!-- <div id="catfield"> //-->
<div id="tree">
<?php
function dump_item(&$item, $id) {
    if ( !empty($item['children']) ) {
	$numex = count(array_filter(explode(',',$item['catExperts'])));
	echo '<li id="id_'.$id.'">'.$item['name'].
	' <small>('.$numex.' experts)</small>'."\n".
	'<ul>';
	foreach($item['children'] as $k => $v) {
	    dump_item($v, $v['id']);
	}
	echo '</ul>'."\n".
	'</li>'."\n";
    } else {
	$numex = count(array_filter(explode(',',$item['catExperts'])));
	echo '<li id="id_'.$id.'">';
	if ($numex <= 0) { echo '<font color="red">'; } 
	echo $item['name'];
	if ($numex <= 0) { echo '</font>'; }
	echo ' <small>('.$numex.' experts)</small>'.'</li>'."\n";
    }
    return;
}
echo '<ul>'."\n";
foreach($cat as $k => $v) {
    dump_item($v, $v['id']);
}
echo '</ul>'."\n";
?>
</div>
</td></tr></table>
<form method="post" action="" id='myform' name="myform">
<input type="hidden" name="do"           value="modifycategory">
<input type="hidden" name="catAction"    id="catAction"      value=""> 
<input type="hidden" name="catID"        id="catID"          value="">
<input type="hidden" name="catName"      id="catName"        value="">
<input type="hidden" name="catWeight"    id="catWeight"      value="">
<input type="hidden" name="catOperation" id="catOperation"   value="">
</form>