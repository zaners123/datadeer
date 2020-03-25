<?php
/**
 * Backend for the user index and edit
 */

require_once "/var/www/php/couch.php";

if (isset($_POST["biography"])) {
	require "/var/www/php/requireSignIn.php";
	//main user is uploading biography
	header("Location: edit.php");
	$prof = array(
		"username"=>$_SESSION["username"],
		"biography"=>"",
		//a 160x160 PNG, blank by default
		"icon"=>"",
	);
	$profile = getDoc("profile",$_SESSION["username"],$prof);
	$profile["biography"] = urlencode($_POST["biography"]);
	setDoc("profile",$_SESSION["username"], $profile);
} else if (isset($_POST["isicon"])) {
	require "/var/www/php/requireSignIn.php";
	//main user is uploading icon
	/*echo "\$_FILES is ";
	var_dump($_FILES);*/
	// Undefined | Multiple Files | $_FILES Corruption Attack
	// If this request falls under any of them, treat it invalid.
	if (
		!isset($_FILES['icon']['error']) ||
		is_array($_FILES['icon']['error'])
	) {
		echo 'Invalid parameters.';return;
	}
	// Check $_FILES['upfile']['error'] value.
	switch ($_FILES['icon']['error']) {
		case UPLOAD_ERR_OK:
			break;
		case UPLOAD_ERR_NO_FILE:
			echo 'No file sent.';return;
		case UPLOAD_ERR_INI_SIZE:
		case UPLOAD_ERR_FORM_SIZE:
			echo 'Exceeded filesize limit.';return;
		default:
			echo 'Unknown errors.';return;
	}

	//main safely get image base64
	//error_log("NAME\"".$_FILES["icon"]["tmp_name"]."\"");

	//test if its an image
	if(($size = getimagesize($_FILES["icon"]["tmp_name"])) === false) {
		echo "not image";return;//not image
	}
	if ($size[0] > 2048 || $size[1] > 2048) {
		echo "Way too big of an image!";return;
	}

	$path = $_FILES["icon"]["tmp_name"];


	//get info of file
	$type = strtolower(pathinfo($_FILES["icon"]["name"], PATHINFO_EXTENSION));


	//main set it to a constant 100px by 100px
	$data = file_get_contents($path);
	$imgFrom = imagecreatefromstring($data);
	$imgTo = imagecreatetruecolor(100,100);
	imagesavealpha($imgTo, true);
	imagefill($imgTo, 0, 0, imagecolorallocatealpha($imgTo,255,255,255, 0));
	imagecopyresampled($imgTo, $imgFrom, 0, 0, 0, 0, 100, 100, $size[0], $size[1]);

	//put info in base64 format
	ob_start();
	imagepng($imgTo);
	$base64 = makeImageBase64(ob_get_clean(),$type);

	//set profile
	$profile = getDoc("profile",strtolower($_SESSION["username"]),$prof);
	$profile["icon"] = $base64;
	setDoc("profile",strtolower($_SESSION["username"]), $profile);

	//success
	header("Location: edit.php");
}
function makeImageBase64($data, $type) {
	return "data:image/".$type.";base64,".base64_encode($data);
}