<?php
require "/var/www/php/requireSignIn.php";
header('Content-type: text/plain');

//client gives ID, gametype, this gets respective board

$id = filter_input(INPUT_GET,"id",FILTER_VALIDATE_INT);
$gametype = filter_input(INPUT_GET,"gametype",FILTER_VALIDATE_INT);
if (!$id || $gametype) exit("Give board ID");

require "lib.php";
//todo choose board based off of gameType
$o = new MinesweeperBoard();
$o->constructById($id);

$x = filter_input(INPUT_GET,"x",FILTER_VALIDATE_INT);
$y = filter_input(INPUT_GET,"y",FILTER_VALIDATE_INT);
if ($x && $y) {
	$o->respondToClick($x,$y);
} else {
	echo "ERR X or Y format";
}

echo $o->getSanatizedBoard();