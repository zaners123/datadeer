<?php
/**
 * Used by Game API (for minesweeper and Sudoku, so far)
 * To use, send GET.id, GET.game, and GET.json

 */
require "/var/www/php/requireSignIn.php";
header('Content-type: text/plain');
if (!isset($_GET["id"])) exit("set id to game board ID");
if (!isset($_GET["game"])) exit("set GET[game] to either 'minesweeper' or 'sudoku'");
if (!isset($_GET["json"])) exit("set json. In minesweeper and sudoku, should be {x:0,y:0}");
if (!preg_match("/^(\d+)$/",$_GET["id"],$id)) exit("set id");
$id=$id[1];
if ($_GET["game"]=="sudoku") {
	$game = "sudoku";
	$board = new SudokuBoard();
} else if ($_GET["game"]=="minesweeper") {
	$game = "minesweeper";
	$board = new MinesweeperBoard();
} else {
	exit("Unknown GET[game], use 'minesweeper','sudoku',etc");
}
require "lib.php";
$board->constructById($id);
$board->
echo $board->getSanatizedBoard();