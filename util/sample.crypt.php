<?php

include_once("config.util.php");


function my_crypt($a) {
    $b = config_encrypt($a);
    $c = config_decrypt($b);
    echo $a.' => '.$b.' => '.$c."\n";
}

$vals = array('onldb2.starp.bnl.gov', '3701', 'ShiftSignup','sign-up', 'orion.star.bnl.gov', '3306', 'nobody2', 'drupal', 'onldb.starp.bnl.gov', '3501');

foreach($vals as $k => $v) {
    my_crypt($v);
}