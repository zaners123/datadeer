<?php
require_once "lib.php";
$conn = mysqli_connect("localhost","website",parse_ini_file("/var/www/php/pass.ini")["mysql"],"md5");

//start on clean slate?
//mysqli_query($conn,'delete from hashes');
//add($conn,"");

//set_time_limit(5);
$alphanumericPlus = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()_-+=[]{}\/|;:\'",<>.?`~';
$lowercase = 'abcdefghijklmnopqrstuvwxyz';

$setToUse = $alphanumericPlus;
$lenFrom = 3;
$length = 4;

ini_set("output_buffering","off");

ob_implicit_flush();
while (ob_get_level()) {
    ob_end_clean();
}

feed($conn, $alphanumericPlus, $length,$lenFrom);