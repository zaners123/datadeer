<?php
require "flightLib.php";
$info = json_decode(base64_decode($_GET["info"]),true);
header("Content-Type: image/png");

$width=700;
$height=200;

$im = @imagecreate($width, $height)
or die("Cannot Initialize new GD image stream");

$bgColor = imagecolorallocate($im, 243, 220,244);
$fgColor = imagecolorallocate($im, 0, 0, 20);

imagefilledrectangle($im,0,0,$width,$height,$fgColor);
imagefilledrectangle($im,5,5,$width-5,$height-5,$bgColor);
//title
imagestring($im, 5, 10, 10,  "Deer Air: ".$info["class"]." ".(isset($info["date_return"])?"Round-trip":"One Way")." Ticket", $fgColor);

//print t1,t2
imagestring($im, 5, 15,30,  "From: ".htmlspecialchars($info["flight_from"]), $fgColor);
imagestring($im, 5, 15,45,  "To: ".htmlspecialchars($info["flight_to"]), $fgColor);
imagestring($im, 5, $width/2,30,  "Departure: ".htmlspecialchars($info["date_depart"])." ".$info["t1"]."-".$info["t2"], $fgColor);
if (isset($info["date_return"])) {
	imagestring($im, 5, $width/2, 45, "Return: " . htmlspecialchars($info["date_return"]) . " " . $info["t3"]."-".$info["t4"], $fgColor);
}

imagestring($im, 5, 15,150,  "Boarding ".htmlspecialchars($info["people"])." ".($info["people"]>1?"people":"person"), $fgColor);

imagepng($im);
imagedestroy($im);