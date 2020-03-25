<?php
//main for subscriber-only downloads in /var/www/subonly (SYNC CODE TO share/d.php)
require_once "/var/www/php/requireSubscription.php";
if (!isset($_GET["q"])) {
	header("Location: /");
	exit;
}

//log download
require "/var/www/php/lib.php";
securityLog("User downloading \"".$_GET["q"]."\"");

//safely gets filename (ALLOW PERIOD HERE, NOT IN share/d.php)
$file = preg_replace("/[^a-zA-Z0-9\.]/","",$_GET["q"]);


$filepath = "/var/www/subonly/".$file;
//tells browser to download this
header('Content-Disposition: attachment; filename="'.$file.'"');
header("Content-Type: ".mime_content_type($filepath));

//output file contents
$handle = fopen($filepath, "r");
echo fread($handle, filesize($filepath));
fclose($handle);