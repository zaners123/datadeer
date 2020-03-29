<?php
require_once "/var/www/html/game/v2/lib.php";

/**
	Board format is {{"answer" board},{"hidden" board (same as answer board but got some zeroes)},{"client" board}}
	Iff client === grid, then call gameWon();


 */
class SudokuBoard extends GameBoard {

	public function __construct() {}

	protected $gametype = GameBoard::SUDOKU;

	public function getClientBoard() {return $this->board["client"];}
	public function getHiddenBoard() {return $this->board["hidden"];}
	public function getAnswerBoard() {return $this->board["answer"];}

	/**@param string $board - length of 81 of digits 0-9*/
	private function verifyBoard($board) {
		$good = "123456789";
		//first check rows and columns
		for($cols=0;$cols<2;$cols++) {
			for ($i=0;$i<9;$i++) {
				$val = "";
				for ($inc=0;$inc<9;$inc++) {
					if ($cols) {
						$val.=$board[$i + $inc*9];
					} else {
						$val.=$board[$i*9 + $inc];
					}
				}
				$val = str_split($val);
				sort($val);
				if ($val = implode('',$val) != $good) {
					error_log("Bad board #".$this->id." ".($cols?"col":"row")." #".$i." is ".$val);
					return false;
				}
			}
		}
		//check 3x3 boxes
		for ($boxY=0;$boxY<3;$boxY++) {
			for($boxX=0;$boxX<3;$boxX++) {
				$box = "";
				for ($indexX=0;$indexX<3;$indexX++) {
					for($indexY=0;$indexY<3;$indexY++) {
						$box .= $board[($boxX*3+$indexX)+($boxY*3+$indexY)*9];
					}
				}
//				error_log(json_encode($box));
				$box = str_split($box);
				sort($box);
				if (($box = implode("",$box)) != $good) {
					error_log("Bad box #".$this->id." (".$boxX.",".$boxY.") is ".$box);
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * TODO although this currently makes a solvable board, it does not make a very random one (only 9 possible boards...)
	 *      Possible "cycles" are:
	 *          Shuffle rows or columns of the same group (ex: rows 0 and 1 (THEY HAVE TO BE OF THE SAME MOD 3!))
	 *          Shuffle rows-groups or column-groups (ex: rows 0,1,2 with rows 3,4,5 (All 3 have to move!))
	 *          Flip on x or y axis
	 *          Rotate by 90, or 180 degrees
	 *          Flip on y=x "diagonal" axis
	 * @return array of length 3 of string of len81, containing numbers 1-9 (inclusive).
	*/
	private function generateBoard($hints) {
		$gen = "";
		$ret = array();
		//start with one row
		$randomRow = str_split("123456789");
		shuffle($randomRow);
		$nextRow = "";
		for($x=0;$x<9;$x++) $nextRow=$nextRow.$randomRow[$x];
//		error_log($nextRow);
		$gen .= $nextRow;
		//gen next 8 rows
		for($y=1;$y<9;$y++) {
			$lastRow = $nextRow;
			$delta = $y%3?3:1;
			for($x=0;$x<9;$x++) $nextRow[$x] = $lastRow[($x+$delta)%9];
			$gen .= $nextRow;
//			error_log($nextRow);
		}
		if (!$this->verifyBoard($gen)) {
			exit("GENERATOR FAILURE, PWEASE TELL ME");
		}
		$ret["answer"] = $gen;
		$ret["hidden"] = $ret["answer"];

		//removes random values from the board, to make it harder
		//todo verify it has one solution
		$remove = 81-$hints;
		while ($remove > 0) {
			$i = rand(0,80);
			if ($ret["hidden"][$i]!=" ") {
				$ret["hidden"][$i]=" ";
				$remove--;
			}
		}
		//client initially is equal to hidden map
		$ret["client"] = $ret["hidden"];
		return $ret;
	}

	/**
	Size passed as (length; regular grid is 3),(diff; 0 easy, 1 normal, 2 hard), maybe type of board or something, like 3D
	 */
	public function populateByGenerate($size) {
		//todo care about size (maybe). Size could be hints
		$hints = 50;
		$this->size = $size;
		$this->board = $this->generateBoard($hints);
		parent::populateByGenerate($size);
	}

	public function takeInput($input) {
		//input is entire client board client[0..n]
		$clBoard = $input["board"];
		//store new client sudoku board

//		error_log($clBoard);

		if (!preg_match("/^[0-9 ]{81}$/",$clBoard)) exit("Bad board");
//		error_log(json_encode($this->board));
		$this->board["client"] = $clBoard;
		//see if they have won or not (test for mismatches client side)
		$won = $this->board["answer"]==$this->board["client"];
//		$won = true;
		//todo test if board is both a valid board and contains the original board (incase given board had two solutions)



		if ($won) $this->setGameToWon();
		//update changes
		$this->sqlUpdateBoard();
	}

	public function getSanitizedBoard() {
		$ret = $this->board;
		unset($ret["answer"]);
		return json_encode(array(
			"state"=>$this->isWon()?"DONE":"",
			"board"=>$ret
		));
	}

	public function parseSize() {
		return array("width"=>9,"height"=>9);
		//todo more sizes (maybe, probs not tho)
	}
}