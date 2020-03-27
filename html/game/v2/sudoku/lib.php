<?php
require_once "/var/www/html/game/v2/lib.php";

/**
	Board format is {{"answer" board},{"hidden" board (same as answer board but got some zeroes)},{"client" board}}
	Iff client === grid, then call gameWon();


 */
class SudokuBoard extends GameBoard {

	public function __construct() {}

	/**
	Size passed as (length; regular grid is 3),(diff; 0 easy, 1 normal, 2 hard)
	 */
	public function populateByGenerate($size) {
		$this->gametype = 4;
		$this->size = $size;
		//testing imma just assume size is 3 and diff is really easy (im bad at sudoku)
//		if (!isset($size["level"])) exit("json must contain level");
		$this->board = array();
		$this->board["answer"] = str_pad("",81,"0");

		//main actually generate a board
		$randomRow = shuffle(str_split("123456789"));
		$firstRow = "";
		for($x=0;$x<9;$x++) $firstRow=$firstRow.$randomRow[$x];
		var_dump($firstRow);
		exit("TESTING lol");

		$this->board["hidden"] = $this->board["answer"];

		//todo filter hidden
		$this->board["client"] = $this->board["hidden"];
		parent::populateByGenerate($size);
	}

	public function takeInput() {
		//todo (see Minesweeper on howto get GET input haha get it)
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
		$ret = $this->board;
		//duh, lol
		unset($ret["answer"]);
		return $ret;
	}
}