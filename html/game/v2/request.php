<?php
require "/var/www/php/requireSignIn.php";
header('Content-type: text/plain');

//client gives ID, gametype, this gets respective board

$id = filter_input(INPUT_GET,"id",FILTER_VALIDATE_INT);
$gametype = filter_input(INPUT_GET,"gametype",FILTER_VALIDATE_INT);
if (!$id || !$gametype) exit("Give board ID and gametype");
require_once "lib.php";
//choose board based off of gameType
switch ($gametype) {
	case GameBoard::MINESWEEPER:
		require_once "minesweeper/lib.php";
		$game = new MinesweeperBoard();
		break;
	case GameBoard::SUDOKU:
		require_once "sudoku/lib.php";
		$game = new SudokuBoard();
		break;
	case GameBoard::TICTACTOE:
		require_once "tictactoe/lib.php";
		$game = new TicTacToeBoard();
		break;
	case GameBoard::POKER:
		require_once "poker/lib.php";
		$game = new PokerBoard();
		break;
	default:
		exit("Unknown gametype");
		break;
}

$game->populateFromID($id);

$game->takeInput();

echo $game->getSanitizedBoard();