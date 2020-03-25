<?php
//main used for downloading /var/www/share/ files (SYNC CODE TO golduser/d.php)

/**

 $_GET[q] is the server-side file name such as "alksjdfhksaf"
 $_GET[n] is the user-given name such as "cat.png"

 */


if (!isset($_GET["q"]) || !isset($_GET["n"])) {
	header("Location: /");
	exit;
}

//log download
require "/var/www/php/lib.php";
securityLog("User downloading \"".$_GET["q"]."\"");

//safely gets filename
$file = preg_replace("/[^a-zA-Z0-9]/","",$_GET["q"]);
$displayname = preg_replace("/[^a-zA-Z0-9\.]/","",$_GET["n"]);

$filepath = "/var/www/share/".$file;
//tells browser to download this
header('Content-Disposition: attachment; filename="'.$displayname.'"');
header("Content-Type: ".mime_content_type($filepath));

//output file contents
$handle = fopen($filepath, "r");
echo fread($handle, filesize($filepath));
fclose($handle);