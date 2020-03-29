<?php
require "/var/www/php/requireSignIn.php";
header('Content-type: text/plain');

//client gives ID, gametype, this gets respective board

$id = filter_input(INPUT_GET,"id",FILTER_VALIDATE_INT);
$gametype = filter_input(INPUT_GET,"gametype",FILTER_VALIDATE_INT);
if (!$id || !$gametype) exit("Give board ID and gametype");

require_once "lib.php";
require_once "minesweeper/lib.php";
require_once "sudoku/lib.php";
require_once "tictactoe/lib.php";
//choose board based off of gameType
switch ($gametype) {
	case GameBoard::MINESWEEPER:
		$game = new MinesweeperBoard();
		$game->populateFromID($id);
		break;
	case GameBoard::SUDOKU:
		$game = new SudokuBoard();
		$game->populateFromID($id);
		break;
	case GameBoard::TICTACTOE:
		$game = new TicTacToeBoard();
		$game ->populateFromID($id);
		break;
	default:
		exit("Unknown gametype");
		break;
}

if (isset($_POST)) {
	$game->takeInput($_POST);
} else {
	$game->takeInput($_GET);
}

echo $game->getSanitizedBoard();