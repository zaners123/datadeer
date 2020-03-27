<?php
require "/var/www/php/startSession.php";

/**
	May occasionally just yell and exit
 */
function saveAsset($userfile, $folder,$maxBits) {
//	$fileExtension = pathinfo($userfile["name"],PATHINFO_EXTENSION);
	if ($userfile["size"] > $maxBits) {
		exit("ERR: File too big");
	}

	$filename = "";
	$keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$keyspacelen = strlen($keyspace);
	//NOT CRYPTOGRAPHIC, doesnt need to be tho
	for ($i = 0; $i < 20; ++$i) {
		$filename .= $keyspace[rand(0, $keyspacelen-1)];
	}

	securityLog("Uploading file \"".$filename."\"");

	$uploadTo = $folder.$filename;
	//main user is uploading file

	// Undefined | Multiple Files | $_FILES Corruption Attack
	// If this request falls under any of them, treat it invalid.
	if (
		!isset($userfile['error']) ||
		is_array($userfile['error'])
	) {
		exit("ERR: Invalid parameters.");
	}

	//main check FILETYPE
	/*$bannedExtension = array("cgi","shtml","xhtml","html","htm","pl","htaccess","phar","php","phtml","phps");
	if (in_array($fileExtension,$bannedExtension)) {
		http_response_code(415);
		return "ERR: Invalid file type";
	}
	if (strlen($userfile["name"] > 100)) {
		http_response_code(415);
		return "ERR: Too Long File Name";
	}*/

	// Check $_FILES['upfile']['error'] value.
	switch ($userfile['error']) {
		case UPLOAD_ERR_OK:
			break;
		case UPLOAD_ERR_NO_FILE:
			exit('ERR: No file sent.');
		case UPLOAD_ERR_INI_SIZE:
			exit('ERR: Exceeded filesize limit.');
		case UPLOAD_ERR_FORM_SIZE:
			exit('ERR: Exceeded the filesize limit.');
		default:
			exit('ERR: Unknown errors.');
	}

	error_log("MOVING FROM ".$userfile["tmp_name"]." TO ".$uploadTo);
	if (!move_uploaded_file($userfile["tmp_name"],$uploadTo)) {
		exit("ERR: move failed");
	}
	return $filename;
}



//main This is only here for legal reasons (in case I had to say who uploaded something illegal to datadeer.net/share)
function securityLog($message) {
	//prepend with location and date
	$logText = $_SERVER["REMOTE_ADDR"].":".$_SERVER["REMOTE_PORT"]." [".date("F j, Y, g:i a")."] ";
	if (isset($_SESSION) && isset($_SESSION["username"])) {
		$logText.="[".$_SESSION["username"]."] ";
	} else {
		$logText.="[?] ";
	}
	$logText .= $message."\n";
	file_put_contents("/var/log/legal/user".date("n-j-Y").".log", $logText, FILE_APPEND);
}