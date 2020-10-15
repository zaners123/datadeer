<?php
require_once "lib.php";
$conn = mysqli_connect("localhost","website",parse_ini_file("/var/www/php/pass.ini")["mysql"],"md5");

//set_time_limit(5);
$alphanumericPlus = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()_-+=[]{}\/|;:\'",<>.?`~';
$lowercase = 'abcdefghijklmnopqrstuvwxyz';

$setToUse = $alphanumericPlus;
$length = 2;
//exit();

//start on clean slate?
//mysqli_query($conn,'delete from hashes');
//add($conn,"");

feed($conn, $alphanumericPlus, 2);
//var_dump($res->fetch_all());

/*foreach (arrayPermutation($setToUse, $length) as $y) {
    $res = mysqli_query($conn,'insert into hashes(`in`,`out`) values ("'.$y.'",unhex("'.md5($y).'"));');
}*/