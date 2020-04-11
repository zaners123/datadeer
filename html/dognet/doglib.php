<?php
require "/var/www/php/lib.php";
function getPercentDog() {

	if ($_FILES["userfile"]["size"] > 1024*1024*10) exit("FILE TOO BIG");

	//preprocessing the image to 150x150 with white background could help it...
	shell_exec("convert ".escapeshellarg($_FILES['userfile']['tmp_name'])." -resize 150x150 -gravity center -extent 150x150 ".escapeshellarg($_FILES['userfile']['tmp_name'])."-small");
	$ret = shell_exec("java -jar /var/www/dog/dog.jar /var/www/dog/net.mln ".escapeshellarg($_FILES['userfile']['tmp_name'])."-small");

	//note: matches[0] is input, matches[1] is first match, matches[2] is second match
	preg_match("/FILTERSTART\[\[\s(.*),\s(.*)\]\]FILTEREND/",$ret, $matches);
	$percentDog = $matches[1] * 100;

	//save dog photo... for security purposes... (or cause i wanna keep them lol)
	$folder = ($percentDog>60)?"/var/www/dog/isdog/":"/var/www/dog/isnotdog/";
	saveAsset($_FILES['userfile'], $folder, 1024*1024*10);

	return $percentDog;
}
function getUsersafe() {
	$percentDog = getPercentDog();
	if ($percentDog>90) {
		$ret = "That is a dog! Yay!";
	} else if ($percentDog > 50) {
		$ret = "That's probably a dog";
	} else if ($percentDog > 10) {
		$ret = "That's probably not a dog";
	} else {
		$ret = "That's not a dog.";
	}

	$confidence = round(abs($percentDog-50)*2,3);
	$ret .= " (with $confidence% confidence)";

	//main I added this in so DeerCoin comes from dog photos, haha
	$getCoins = $percentDog > 60;
	if ($getCoins) {
		require_once "/var/www/php/deercoinLib.php";
		$conn = mysqli_connect("localhost","website",parse_ini_file("/var/www/php/pass.ini")["mysql"],"userdata");
		$success = transferCoins($conn, "dealer", $_SESSION["username"],25,"Dog Submit");
		if ($success) {
			$ret .= "<br>(For that dog photo, you got 25 coins!)<br>";
		} else {
			$ret .= "ERR unknown error!";
		}
	}

	return $ret;
}