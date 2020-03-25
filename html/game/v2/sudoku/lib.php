<?php
require "../lib.php";
/**
	Board format is {{grid [9,1,6,4,8...]},{bitmasks where 1 equals visible...},{client[9,1,8,5...]}}
	Iff client === grid, then call gameWon();


 */
class SudokuBoard extends GameBoard {

	public function __construct()
	{
		parent::__construct("sudoku");
	}

	public function constructByGenerate($data) {
		if (!isset($data["level"])) exit("json must contain level");
		if ($data["level"]=="normal") {
			//todo how the heck do i make a sudoku board
		} else {
			exit("Set level to valid level, such as normal");
		}
		$this->sqlInsertBoard();
	}

	public function takeInput($input) {
		//input is entire client board client[0..n]

		//todo store new client sudoku board and process hints. Also, see if they have won or not (test for mismatches client side)

		$won = true;
		for($x=0;$x<$this->board["grid"]["size"];$x++) {
			//if wrong in any way, wrong = true. Client would also know since most of the board would be red
			if ($this->board["grid"][$x] !== $this->board["client"][$x]) {
				$won = false;
				break;
			}
		}
		if ($won) $this->setGameToWon();
		//update changes
		$this->sqlUpdateBoard();
	}

	public function getSanatizedBoard() {
		//return clientBoard
		$entire = $this->board;
	}
}