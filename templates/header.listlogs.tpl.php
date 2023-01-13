<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">

<html id="st_shifts_html">
<head>
    <title>sPHENIX Shift Signup</title>
    <META HTTP-EQUIV="expires" CONTENT="Wed, 26 Feb 1997 08:21:57 GMT"> 
    <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
    <link href="css/styles.css" type="text/css" rel="stylesheet">
    <link href="css/jquery.ui.all.css" type="text/css" rel="stylesheet">
    <link href="css/demo_table.css" type="text/css" rel="stylesheet">
    <script type="text/javascript" src="js/jquery-1.4.4.min.js"></script>
    <script type="text/javascript" src="js/jquery-ui-latest.min.js"></script>
    <script type="text/javascript" src="js/jquery.dataTables.min.js"></script>
    <script type="text/javascript">   
	$(document).ready(function () {    	
	    $('#dyntbl').dataTable({
		    "aaSorting": [[ 0, "desc" ]],
                    "bJQueryUI": true,
                    "iDisplayLength": 15,
		    "bSortClasses": false,
                    "sPaginationType": "full_numbers"
                });
	});
    </script>
</head>

<body id="st_shifts_body">
