<?php

if (!isset($_GET["id"])) return;
$id = urlencode($_GET["id"]);
if (preg_match("/\w/",$id)) {
	//v1 ID
	header("Location: /game/v1/gameView.php?id=".$id);
} else {
	//v2 ID
	require "lib.php";
	$conn = mysqli_connect("localhost","website",parse_ini_file("/var/www/php/pass.ini")["mysql"],"userdata");
	$gametype = GameBoard::getGameType($conn, $id);
	header("Location: /game/v2/" . GameBoard::FOLDERS[$gametype] . "/play.php?id=".$id);
}
