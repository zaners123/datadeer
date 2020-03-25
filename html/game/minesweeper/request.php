<?php
/**
 * Minesweeper trash. @deprecated by requestAPI


 */


require "/var/www/php/requireSignIn.php";
header('Content-type: text/plain');
if (!isset($_GET["id"])) exit("set id");
if (!preg_match("/^(\d+)$/",$_GET["id"],$id)) exit("set id");
$id=$id[1];
require "lib.php";
$o = new MinesweeperBoard();
$o->constructById($id);

if (isset($_GET["x"]) && isset($_GET["y"])) {
	if (!preg_match("/(\d+)/",$_GET["y"],$y)) exit("err y format");
	if (!preg_match("/(\d+)/",$_GET["x"],$x)) exit("err x format");
	$x=$x[1];
	$y=$y[1];
	$o->respondToClick($x,$y);
}
echo $o->getSanatizedBoard();