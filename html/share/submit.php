<?php
$readOnlyDisabled = true;
require "/var/www/php/requireSignIn.php";
require "/var/www/php/couch.php";
require_once "/var/www/php/subdata.php";
require "/var/www/php/sprinklerLib.php";

//TODO only allow files if space left in {for-loop over $doc} < 50MB

/**
main input $_FILES["userfile"] for file data, extention, size
$_POST["public"]==="on" to choose if it should be public
 */


if (isset($_FILES["userfile"])) {
	$sizeBytes = $_FILES['userfile']["size"];
	$fileExtension = pathinfo($_FILES['userfile']["name"],PATHINFO_EXTENSION);

	$doc = getDoc("share",$_SESSION["username"],$blankDefault);

	//space used includes current file
	$spaceUsed = $sizeBytes;

	//sum space of all files
	foreach (sanitiseDoc($doc) as $file) {
		$spaceUsed += $file["size"];
	}

	//main return if they have reached their quota
	if (isSubscribed()) {
		//20GB max space subs
		if ($spaceUsed > 1024 * 1024 * 1024 * 20) exit("File quota reached (gold)");
	} else {
		//100MB max space not subs
		if ($spaceUsed > 1024 * 1024 * 100) exit("File quota reached (not gold)");
	}

	//main check filesize
	if (isSubscribed()) {
		//256MB max gold
		$maxBits = 1024*1024*256;
	} else {
		//50MB max for not gold
		$maxBits = 1024*1024*50;
	}

	$filename = saveAsset($_FILES['userfile'], "/var/www/share/", $maxBits);

	$doc[$filename]["name"] = $_FILES['userfile']["name"];
	$doc[$filename]["ext"] = $fileExtension;
	$doc[$filename]["size"] = $sizeBytes;
	$isPublic = isset($_POST["public"]) && $_POST["public"]==="on";
	$doc[$filename]["public"] = ($isPublic)?"true":"false";
	if ($isPublic) {
		$doc[$filename]["hits"] = 0;
	}

	if (preg_match("/^ERR/",$filename)) {
		//display error
		echo $filename;
	} else {
		//save info in doc
		setDoc("share",$_SESSION["username"],$doc);
		header("Location: /share/upload.php");
	}
} else {
	echo "error no file. This could be because you forgot to select a file, or your file is much to big";
}