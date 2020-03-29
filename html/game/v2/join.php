<?php
require "lib.php";

$conn = mysqli_connect("localhost","website",parse_ini_file("/var/www/php/pass.ini")["mysql"],"userdata");
$id = filter_input(INPUT_GET,"id",FILTER_VALIDATE_INT);
$gametype = GameBoard::getGameType($conn, $id);
header("Location: /game/v2/" . GameBoard::FOLDERS[$gametype] . "/play.php?id=".$id);
