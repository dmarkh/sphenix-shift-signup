<?php

function dump_child_category(&$v, $prevName = '') {

    if (!empty($v['persons'])) {
	  foreach ($v['persons'] as $k2 => $v2) {
		$divider = $prevName;
		if ( empty($divider) ) { $divider = $v['catName']; }
  		$output = '<li data-divider="'.$divider.'">'
    	  .'<img src="img/icons/crew_detector_operator.png">'
    	  .'<h2 style="margin: 0px; padding: 0px;"><font color="brown">'.$v['catName'].'</font> :: <font color="green">'.$v2['exFirstName'].' '.$v2['exLastName'].'</font></h2>';
		$attr = array(); $attr2 = array();
		if ( !empty($v2['exPhonePrimary']) ) { $attr[] = '<b>Primary Phone:</b> <a href="tel:'.$v2['exPhonePrimary'].'">'. $v2['exPhonePrimary'].'</a>'; }
		if ( !empty($v2['exPhoneCell']) ) { $attr[] = '<b>Cell Phone:</b> <a href="tel:'.$v2['exPhoneCell'].'">'. $v2['exPhoneCell'].'</a>'; }
		if ( !empty($v2['exPhoneHome']) ) { $attr[] = '<b>Home Phone:</b> <a href="tel:'.$v2['exPhoneHome'].'">'. $v2['exPhoneHome'].'</a>'; }
		if ( !empty($v2['exEmail']) ) { $attr2[] = '<b>Email:</b> '. $v2['exEmail']; }
		if ( !empty($v2['exDescription']) ) { $attr2[] = '<b>Note:</b> '. $v2['exDescription']; }
		if ( !empty($attr) ) { $output .= '<p style="margin-top: 5px; margin-bottom: 3px; padding: 0;">'.implode(', ', $attr).'</p>'; }
		if ( !empty($attr2) ) { $output .= '<p style="margin: 0; padding: 0;">'.implode(', ', $attr2).'</p>'; }
		$output .= '</li>'."\n";
		echo $output;
	  }
    }
    if ( !empty($v['children'])) {
	  foreach($v['children'] as $k2 => $v2) {
	    dump_child_category($v2, $v['catName']);
	  }
    }    
}

echo '<form class="ui-filterable">'
      .'<input id="shiftlog-experts-filter-box" data-type="search" placeholder="Search experts..">'
  	  .'</form>'
  	  .'<ul data-role="listview" data-inset="true" id="shiftlog_experts_container" data-theme="a" data-filter="true" data-input="#shiftlog-experts-filter-box">'."\n";

foreach($cat as $k => $v) {
  dump_child_category($v);
}

echo '</ul>';
