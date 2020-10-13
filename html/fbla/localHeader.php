<!DOCTYPE html>
<html lang="en">
<head lang="en">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta property="og:image" content="https://datadeer.net/datadeer.png">
<meta name="description" content="The site for all of your needs.">
<meta charset="UTF-8">
	<link rel="stylesheet" type="text/css" href="fblacss.css"/>
<title id="title">Deer Air</title>
<link href="https://fonts.googleapis.com/css?family=Gloria+Hallelujah|Roboto&display=swap" rel="stylesheet">
<style>
	* {
		font-family: 'Roboto', sans-serif;
		/*font-family: 'Gloria Hallelujah', cursive;*/
	}
</style>
<?php

if ((include '/var/www/php/bodyTop.php') != TRUE) {
	echo "</head><body>";
}?>

<?php require "flightLib.php"; ?>
<h1>Deer Air</h1>
<h2>Make flying fun.</h2>