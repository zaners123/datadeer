<?php
/**
 * Backend for the user index and edit
 */

	header("Location: /other/settings.php");

require_once "/var/www/php/couch.php";
require "/var/www/php/requireSignIn.php";
function makeImageBase64($data, $type) {
	return "data:image/".$type.";base64,".base64_encode($data);
}
if (isset($_POST["biography"])) {
	//main user is uploading biography
//	header("Location: edit.php");
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
	// Undefined | Multiple Files | $_FILES Corruption Attack
	// If this request falls under any of them, treat it invalid.
	if (!isset($_FILES['icon']['error']) || is_array($_FILES['icon']['error'])) {
		echo 'Invalid parameters.';return;
	}
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
	$path = $_FILES["icon"]["tmp_name"];
	$size = getimagesize($path);
	if($size === false) {
		echo "not image";return;//not image
	}
	if ($size[0] > 2048 || $size[1] > 2048) {
		echo "Way too big of an image!";return;
	}
	switch ($size[2]) {
		case IMAGETYPE_PNG:$type = "png";break;
		case IMAGETYPE_JPEG:$type = "jpeg";break;
		case IMAGETYPE_GIF:$type="gif";break;
		default:echo "BAD IMAGE";return;
	}
	$imagedata = file_get_contents($path);
	$imgFrom = imagecreatefromstring($imagedata);
	$imgTo = imagecreatetruecolor(100,100);
	imagesavealpha($imgTo, true);
	imagealphablending($imgTo, false);
	imagefill($imgTo, 0, 0, imagecolorallocatealpha($imgTo,255,255,255, 0));
	imagecopyresampled($imgTo, $imgFrom, 0, 0, 0, 0, 100, 100, $size[0], $size[1]);
	//put info in base64 format
	ob_start();
	imagepng($imgTo);
	$base64 = makeImageBase64(ob_get_clean(),$type);
	//set profile
	$profile = getDoc("profile");
	$profile["icon"] = $base64;
	setDoc("profile",strtolower($_SESSION["username"]), $profile);
	//success
//	header("Location: edit.php");
}