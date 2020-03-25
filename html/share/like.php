<?php
require "/var/www/php/requireSignIn.php";
require "/var/www/php/couch.php";

if (!isset($_GET["n"])) {
	echo "How'd you get here?"; exit;
}

//a list of document ID's (each doc is a user)
$db = getDatabase("share")["rows"];

foreach ($db as $user) {
	//main get each user's doc (list of uploaded files)
	$doc = json_decode(getDocUnsafe("share", $user["id"]), true);
	if (!isset($doc[$_GET["n"]])) {
		continue;
	}

	//main like it, then break 2
	if (isset($doc[$_GET["n"]]["likes_by"]) && isset($doc[$_GET["n"]]["likes_by"][$_SESSION["username"]])) {
		//unlike
		unset($doc[$_GET["n"]]["likes_by"][$_SESSION["username"]]);
		setDoc("share",$user["id"],$doc);
		exit("UNLIKED");
	}
	$doc[$_GET["n"]]["likes_by"][$_SESSION["username"]] = "Y";
	setDoc("share",$user["id"],$doc);
	//main file liked
	exit("LIKED");
}
exit("Nothing happened?");