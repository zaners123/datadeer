<?php
require "/var/www/php/requireSignIn.php";
require_once "/var/www/php/couch.php";

function dumpy($mixed = null) {
	ob_start();
	var_dump($mixed);
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}
$info = json_decode(file_get_contents("php://input"),true);
if ($info==null) {
	return;
}
//error_log(dumpy($info));

//main get the doc then apply the changes
$doc = getDoc("tracker");

if (!isset($doc["info"])) {
	$doc["info"] = array();
}

$doc["info"] =array_replace_recursive($doc["info"],$info);

setDoc("tracker",$_SESSION["username"],$doc);