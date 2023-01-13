<center>
<h3>Note: you can zoom any region of the graphs: click on image, hold left mouse button and select required zoom level. 
<br>Double-click on image to zoom-out</H3>
<table>
<tr><td align="center" colspan=2><b>Signup Statistics - including trainee slots (see below)</b></td></tr>
<tr>
    <td style="padding: 10px;"><div id="placeholder1" style="width: <?=$width ?>px; height: <?=$height ?>px; position: relative;"></div></td>
    <td style="padding: 10px;"><div id="placeholder2" style="width: <?=$width ?>px; height: <?=$height ?>px; position: relative;"></div></td>
</tr>
<tr><td align="center" colspan=2><b>Signup Statistics - excluding period coordinator and trainee slots (see below)</b></td></tr>
<tr>
    <td style="padding: 10px;"><div id="placeholder3" style="width: <?=$width ?>px; height: <?=$height ?>px; position: relative;"></div></td>
    <td style="padding: 10px;"><div id="placeholder4" style="width: <?=$width ?>px; height: <?=$height ?>px; position: relative;"></div></td>
</tr>
</table>
    <?php if (!empty($winners)) { ?>
	<h2><img src="img/icons/star.png" border="0" alt="Top 15"> Top 15</h2>
	<table class="st_tbl">
	<tr class="st_tbl_hdr"><td>Place</td><td>Time</td><td>Person</td><td>Institution</td></tr>
	<?php foreach($winners as $k => $v) { ?> 
	    <tr class="<?php if ($k % 2) { echo 'odd'; } else { echo 'even'; }?>"><td align="center"><?php echo ($k+1); ?></td><td align="center"><nobr><?php echo date('r', $v['et']); ?></nobr></td><td align="center"><?=$v['fn'] ?> <?=$v['ln'] ?></td>
	    <td>
	    <?php 
		if (!empty($v['ictr'])) {	
		    $flag = get_flag($v['ictr']);
		    if (!empty($flag)) {
			echo '<img src="img/flags/'.$flag.'" border=0> ';
		    }
		} 
	    ?>
	     <?=$v['inst'] ?></td>
	    </tr>
	<?php } ?>
	</table>
    <?php } ?>

<script id="source" language="javascript" type="text/javascript">
    var opt1 = {
	grid: { background:'#fff', gridLineColor:'#accf9b' },
	seriesColors: [ "#33BB33" ],
	axes:{
    	    xaxis:{
		label: 'Time',
		labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
        	renderer:$.jqplot.DateAxisRenderer, 
		tickRenderer: $.jqplot.CanvasAxisTickRenderer,
		tickOptions:{ 
		    formatString:'%#c',
		    angle: <?=$font_angle ?>,
		    fontSize: '<?=$font_size ?>'
		},
    	    },
	    yaxis:{
		label: 'shift slots taken',
		labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
		min: 0,
		autoscale: true,
		tickOptions:{formatString:'%d'},
	    },
	    highlighter: { show: false },
	},
        series: [
	    {
		color: "<?=$line_color ?>",
		lineWidth: <?=$line_width ?>, 
		showMarker:false,
	    }
	],
	cursor:{ 
	    showCursorLegend:false,
	    intersectionThreshold: 1,
	    show: true, 
	    zoom:true, 
	    showTooltip:true, 
	    constrainZoomTo:'x', 
	    dblClickReset:true 
	} 
    };

    var opt2 = {
	grid: { background:'#fff', gridLineColor:'#accf9b' },
	seriesColors: [ "#33BB33" ],
	axes:{
    	    xaxis:{
		label: 'Time',
		labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
        	renderer:$.jqplot.DateAxisRenderer, 
		tickRenderer: $.jqplot.CanvasAxisTickRenderer,
		tickOptions:{ 
		    formatString:'%#c',
		    angle: <?=$font_angle ?>,
		    fontSize: '<?=$font_size ?>'
		},
    	    },
	    yaxis:{
		label: '% of shift slots taken',
		labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
		ticks: [0,10,20,30,40,50,60,70,80,90,100],
		min: 0,
		max: 100,
		tickOptions:{ 
		    formatString:'%d',
		    fontSize: '<?=$font_size ?>'
		},
	    }
	},
        series: [
	    {
		lineWidth: <?=$line_width ?>,
		showLine: true,
		color: "<?=$line_color ?>",
		fill: true,
		fillAlpha: <?=$fill_alpha ?>,
		fillAndStroke: true,
		fillColor: "<?=$fill_color ?>",
		markerOptions: {
		    show: false,
		},
	    }
	],
	cursor:{ 
	    show: true, 
	    zoom:true, 
	    showTooltip:false, 
	    constrainZoomTo:'x', 
	    dblClickReset:true 
	} 
    };

    var opt3 = {
	grid: { background:'#fff', gridLineColor:'#accf9b' },
	seriesColors: [ "#33BB33" ],
	axes:{
    	    xaxis:{
		label: 'Time',
		labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
        	renderer:$.jqplot.DateAxisRenderer, 
		tickRenderer: $.jqplot.CanvasAxisTickRenderer,
		tickOptions:{ 
		    formatString:'%#c',
		    angle: <?=$font_angle ?>,
		    fontSize: '<?=$font_size ?>'
		},
    	    },
	    yaxis:{
		label: 'shift slots taken',
		labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
		min: 0,
		autoscale: true,
		tickOptions:{formatString:'%d'},
	    },
	    highlighter: { show: false },
	},
        series: [
	    {
		color: "<?=$line_color ?>",
		lineWidth: <?=$line_width ?>, 
		showMarker:false,
	    }
	],
	cursor:{ 
	    showCursorLegend:false,
	    intersectionThreshold: 1,
	    show: true, 
	    zoom:true, 
	    showTooltip:true, 
	    constrainZoomTo:'x', 
	    dblClickReset:true 
	} 
    };

    var opt4 = {
	grid: { background:'#fff', gridLineColor:'#accf9b' },
	seriesColors: [ "#33BB33" ],
	axes:{
    	    xaxis:{
		label: 'Time',
		labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
        	renderer:$.jqplot.DateAxisRenderer, 
		tickRenderer: $.jqplot.CanvasAxisTickRenderer,
		tickOptions:{ 
		    formatString:'%#c',
		    angle: <?=$font_angle ?>,
		    fontSize: '<?=$font_size ?>'
		},
    	    },
	    yaxis:{
		label: '% of shift slots taken',
		labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
		ticks: [0,10,20,30,40,50,60,70,80,90,100],
		min: 0,
		max: 100,
		tickOptions:{ 
		    formatString:'%d',
		    fontSize: '<?=$font_size ?>'
		},
	    }
	},
        series: [
	    {
		lineWidth: <?=$line_width ?>,
		showLine: true,
		color: "<?=$line_color ?>",
		fill: true,
		fillAlpha: <?=$fill_alpha ?>,
		fillAndStroke: true,
		fillColor: "<?=$fill_color ?>",
		markerOptions: {
		    show: false,
		},
	    }
	],
	cursor:{ 
	    show: true, 
	    zoom:true, 
	    showTooltip:false, 
	    constrainZoomTo:'x', 
	    dblClickReset:true 
	} 
    };

    var data1 = [
		<?php 
		    foreach ($res['summary_people'] as $k => $v) {
			echo '['.$v.'],'; 
		    }
		?>
    ];
    var data2 = [
		<?php 
		    foreach ($res['percentage'] as $k => $v) {
			echo '['.$v.'],'; 
		    }
		?>
    ];
    var data3 = [
		<?php 
		    foreach ($res['summary_people2'] as $k => $v) {
			echo '['.$v.'],'; 
		    }
		?>
    ];
    var data4 = [
		<?php 
		    foreach ($res['percentage2'] as $k => $v) {
			echo '['.$v.'],'; 
		    }
		?>
    ];

    var time_min = <?=$res['time_min'] ?>000;
    var time_max = <?=$res['time_max'] ?>000;

    var plot1 = $.jqplot('placeholder1', [data1], opt1);    
    var plot2 = $.jqplot('placeholder2', [data2], opt2);
    var plot3 = $.jqplot('placeholder3', [data3], opt3);    
    var plot4 = $.jqplot('placeholder4', [data4], opt4);
    
</script>


</center>
<BR><BR><BR><BR><BR>

