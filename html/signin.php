<?php
header("Location: /");
require "/var/www/php/sprinklerLib.php";
require_once "../php/Service_Auth.php";
Service_Auth::sing()->authenticate(
	$_POST["username"],
	$_POST["password"],
	isset($_POST["remember"])?$_POST["remember"]:false);