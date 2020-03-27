<?php
require "/var/www/php/lib.php";
function getRawUnprocessed() {
	$folder = "/var/www/dog/";
	//if its more than 40MB, no way is it legit
	$saveRes = saveAsset($_FILES['userfile'], $folder, 1024*1024*10);
	if (strpos($saveRes,"ERR") !== false) exit("ERR");
	if (preg_match("/\W/",$saveRes)) exit("ERR");
	$filename = $folder.$saveRes;

	//note potentially unsafe

	//preprocessing the image to 150x150 with white background could help it...
	shell_exec("convert ".escapeshellarg($filename)." -resize 150x150 -gravity center -extent 150x150 ".escapeshellarg($filename)."-small");
	return shell_exec("java -jar /var/www/dog/dog.jar /var/www/dog/net.mln ".escapeshellarg($filename)."-small");
}
function getPercentDog() {
	$out = getRawUnprocessed();
	//note: matches[0] is input, matches[1] is first match, matches[2] is second match
	preg_match("/FILTERSTART\[\[\s(.*),\s(.*)\]\]FILTEREND/",$out, $matches);
	return $matches[1] * 100;
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
	return $ret;
}