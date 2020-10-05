<?php
/**

$_GET[q] is the server-side file name such as "alksjdfhksaf"

 */
require "/var/www/php/requireSignIn.php";

//check they requested a deletion
if (!isset($_GET["q"])) {
	header("Location: /");
	exit;
}

//safety check
$filename = preg_replace("/[^a-zA-Z0-9]/","",$_GET["q"]);

require "/var/www/php/couch.php";

//main check the user owns that file
$doc = getDoc("share",$_SESSION["username"],$blankDefault);
if (isset($doc[$filename])) {
	//main delete the file
	//keeps file for legal reasons (f off, dont hate me)
	//unlink("/var/www/share/".$filename);
	unset($doc[$filename]);
	setDoc("share",$_SESSION["username"], $doc);
	echo "Deleted";
	header("Location: /share/upload.php");
} else {
	echo "You do not own that file";
}
