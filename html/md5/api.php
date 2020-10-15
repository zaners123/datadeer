<?php
require_once "lib.php";
$conn = mysqli_connect("localhost","website",parse_ini_file("/var/www/php/pass.ini")["mysql"],"md5");

if (isset($_REQUEST['toHash'])) {
    //hashify
    $in = $_REQUEST['toHash'];
    if (!$in || !is_string($in)) {
        exit(json_encode(array("error"=>"Invalid Input")));
    }  else if (strlen($in)>64) {
        exit(json_encode(array("error"=>"Input too long")));
    }
    exit(json_encode(array("out"=>add($conn, $in))));
} else if (isset($_REQUEST["getHash"])) {
    //dehashify
    $in = $_REQUEST["getHash"];
    if (!$in) {
        exit(json_encode(array("error"=>"Invalid Input")));
    } else if (!ctype_xdigit($in)) {
        exit(json_encode(array("error"=>"Input not hexadecimal")));
    } else if (strlen($in) != 32) {
        exit(json_encode(array("error"=>"Input not a MD5 hash (32 hexadecimal characters)")));
    }
    exit(json_encode(array("out"=>get($conn, $in))));

}

//add($conn,"BEANS");
//get($conn, "88bbd77821c34067cbf933f23cc6f6d9");

exit(json_encode(array("error"=>"No Input")));