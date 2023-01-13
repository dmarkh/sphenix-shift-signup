<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">

<html id="st_shifts_html">
<head>
    <title>sPHENIX Shift Signup</title>
    <META HTTP-EQUIV="expires" CONTENT="Wed, 26 Feb 1997 08:21:57 GMT"> 
    <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
    <link href="css/styles.css" type="text/css" rel="stylesheet">
    <link href="js/dynatree/skin/ui.dynatree.css" rel="stylesheet" type="text/css">

    <script language="javascript" src="js/jquery-1.4.4.min.js"></script>
    <script language="javascript" src="js/dynatree/jquery-ui.custom.min.js"></script>
    <script language="javascript" src="js/dynatree/jquery.dynatree.min.js"></script>

<script type='text/javascript'>

  function rename_category() {
    var node = $("#tree").dynatree("getActiveNode");
    if (!node.data.key) return false;
    tmp = node.data.key.split('_');
    title = node.data.title.replace(/(\<small\>\(.*\)\<\/small\>)/g,'');
    var new_name = prompt('Enter new name for this category', title);
    if (new_name != name && new_name != '' && new_name != null) {
        var e1 = document.getElementById('catAction');
        e1.value='rename';
        var e2 = document.getElementById('catID');
        e2.value = tmp[1];
        var e3 = document.getElementById('catName');
        e3.value = new_name;
        document.forms["myform"].submit();
	return true;
    }
    return false;
  }

  function add_child_category() {
    var node = $("#tree").dynatree("getActiveNode");
    if (!node.data.key) return false;
    tmp = node.data.key.split('_');
    var new_name = prompt('Name this category, please', 'New Category');
    if (new_name != null && new_name != '') {
        var e1 = document.getElementById('catAction');
        e1.value='insert';
        var e2 = document.getElementById('catID');
        e2.value = tmp[1];
        var e3 = document.getElementById('catName');
        e3.value = new_name;
        document.forms["myform"].submit();
	return true;
    }
    return false;
  }

  function add_top_category() {
    var new_name = prompt('Name this category, please', 'New Category');
    if (new_name != null && new_name != '') {
        var e1 = document.getElementById('catAction');
        e1.value='insert';
        var e2 = document.getElementById('catID');
        e2.value = 0;
        var e3 = document.getElementById('catName');
        e3.value = new_name;
        document.forms["myform"].submit();
	return true;
    }
    return false;
  }

  function edit_expert_list() {
    var node = $("#tree").dynatree("getActiveNode");
    if (!node.data.key) return false;
    tmp = node.data.key.split('_');
    title = node.data.title.replace(/(\<small\>\(.*\)\<\/small\>)/g,'');
    if (confirm('Do you really want to edit expert list for category ' + title + '?')) {
        var e1 = document.getElementById('catAction');
        e1.value='editexperts';
        var e2 = document.getElementById('catID');
        e2.value = tmp[1];
        document.forms["myform"].submit();
    }
  }

  function delete_category() {
    var node = $("#tree").dynatree("getActiveNode");
    if (node.hasChildren()) {
	alert("Selected category has child nodes. Please delete those first!");
	return false;
    }
    if (!node.data.key) return false;
    tmp = node.data.key.split('_');
    title = node.data.title.replace(/(\<small\>\(.*\)\<\/small\>)/g,'');
    if (confirm('Do you really want to delete category ' + title + '?')) {
        var e1 = document.getElementById('catAction');
        e1.value='delete';
        var e2 = document.getElementById('catID');
        e2.value = tmp[1];
        var e3 = document.getElementById('catName');
        e3.value = name;
        document.forms["myform"].submit();
    }
  }

  function save_category_order() {
    var data = '';
    $("#tree").dynatree("getRoot").visit(function(node){
	tmp = node.data.key.split('_');
	data = data + tmp[1] + ',';
    });
    var e1 = document.getElementById('catAction');
    e1.value='reorder';
    var e2 = document.getElementById('catID');
    e2.value = data;
    document.forms["myform"].submit();
  }
  
  $(function(){
    // Attach the dynatree widget to an existing <div id="tree"> element
    // and pass the tree options as an argument to the dynatree() function:
    $("#tree").dynatree({
      dnd: {
        onDragStart: function(node) {
    	    /** This function MUST be defined to enable dragging for the tree.
            *  Return false to cancel dragging of node.
            */
    	    logMsg("tree.onDragStart(%o)", node);
    	    return true;
        },
        onDragStop: function(node) {
    	    // This function is optional.
    	    logMsg("tree.onDragStop(%o)", node);
        },
        autoExpandMS: 1000,
        preventVoidMoves: true, // Prevent dropping nodes 'before self', etc.
        onDragEnter: function(node, sourceNode) {
    	    /** sourceNode may be null for non-dynatree droppables.
    	    *  Return false to disallow dropping on node. In this case
            *  onDragOver and onDragLeave are not called.
            *  Return 'over', 'before, or 'after' to force a hitMode.
            *  Return ['before', 'after'] to restrict available hitModes.
            *  Any other return value will calc the hitMode from the cursor position.
            */
    	    logMsg("tree.onDragEnter(%o, %o)", node, sourceNode);
    	    // Prevent dropping a parent below it's own child
	                    if(node.isDescendantOf(sourceNode))
	                        return false;
    	    // Prevent dropping a parent below another parent (only sort
    	    // nodes under the same parent)
	                    if(node.parent !== sourceNode.parent)
	                        return false;
	                  if(node === sourceNode)
	                      return false;
    	    // Don't allow dropping *over* a node (would create a child)
    	    return ["before", "after"];
    	},
        onDragOver: function(node, sourceNode, hitMode) {
    	    /** Return false to disallow dropping this node.
            *
            */
    	    logMsg("tree.onDragOver(%o, %o, %o)", node, sourceNode, hitMode);
    	    // Prohibit creating childs in non-folders (only sorting allowed)
	    //        if( !node.isFolder && hitMode == "over" )
	    //          return "after";
        },
        onDrop: function(node, sourceNode, hitMode, ui, draggable) {
    	    /** This function MUST be defined to enable dropping of items on
            * the tree.
            */
    	    logMsg("tree.onDrop(%o, %o, %s)", node, sourceNode, hitMode);
    	    sourceNode.move(node, hitMode);
	    save_category_order();
    	    // expand the drop target
	    //        sourceNode.expand(true);
        },
	onDragLeave: function(node, sourceNode) {
    	    /** Always called if onDragEnter was called.
            */
    	    logMsg("tree.onDragLeave(%o, %o)", node, sourceNode);
        }
      },
      onActivate: function(node) {
        // A DynaTreeNode object is passed to the activation handler
        // Note: we also get this event, if persistence is on, and the page is reloaded.
        // alert("You activated " + node.data.title + ' : ' + node.data.key);
      }
    });
    $("#tree").dynatree("getRoot").visit(function(node){
        node.expand(true);
    });
  });
</script>

</head>

<body id="st_shifts_body">


