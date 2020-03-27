<?php

require_once "/var/www/html/game/v2/lib.php";

//standard competition sizes (changeable at any time)
$boardSizes = [["10x10,6","Practice"],["8x8,10","Novice"],["16x16,42","Intermediate"],["30x16,99","Time Trial"],["30x30,200","Expert"],["100x100,1400","The Gauntlet"]];

class MinesweeperBoard extends GameBoard {
	const MINE = 'm';
	//board could also contain number
	const HIDDEN_FLAG = 'f';
	//SHOWN_EMPTY = 0 through 8;
	const HIDDEN_EMPTY = 'e';


	public function __construct() {}

	private $parsedSize = null;
	/**Outputs minesweeper board size and mine count*/
	function parseSize() {
		if ($this->parsedSize) return $this->parsedSize;
		preg_match("/^(\d+)x(\d+),(\d+)$/",$this->size,$boardSize);
		return $this->parsedSize =
				array("width"=>filter_var($boardSize[1],FILTER_VALIDATE_INT),
					"height"=>filter_var($boardSize[2],FILTER_VALIDATE_INT),
					"mines" =>filter_var($boardSize[3],FILTER_VALIDATE_INT),
				);
	}
	private function placeMines($mines) {
		for ($mine=0;$mine<$mines;$mine++) {
			$mineLoc = rand(0,strlen($this->board)-1);
			if ($this->board[$mineLoc] != self::MINE) $this->board[$mineLoc]=self::MINE;
		}
	}
	/**
	 * Generates new board. Also, inserts it into the table.
	 * @param $size string should contain "width x height , mines" ex "10x10,5"
	 */
	public function populateByGenerate($size) {
		$this->gametype = 3;
		$this->size = $size;
		if (!preg_match("/^\d+x\d+,\d+$/",$size)) exit("Bad minesweeper size");
		$this->board = str_pad("",$this->parseSize()["width"] * $this->parseSize()["height"],self::HIDDEN_EMPTY);
		$this->placeMines($this->parseSize()["mines"]);
		parent::populateByGenerate($size);
	}

	/**
	 * Gets index of (x,y). Is equivalent to (x * width) + y
	 * @return int coord of that character, or -1 if bad index
	*/
	private function at($x,$y) {
		if ($x<0 || $x>=$this->parseSize()["width"] || $y<0 || $y>=$this->parseSize()["height"]) return -1;
		return $x * $this->parseSize()["width"] + $y;
	}
	/**
	 * Given coords, it sees if player dies. If not, it reveals the spot and recurses adjacent zeroes
	 * @param $input array x and y clicked at
	 */
	function takeInput() {
		if (!$this->isActive()) return;
		$x = filter_input(INPUT_GET,"x",FILTER_VALIDATE_INT);
		$y = filter_input(INPUT_GET,"y",FILTER_VALIDATE_INT);

		if ($this->board[$this->at($x,$y)] == self::MINE) {
			//clicked a mine, boom!
			$this->setGameToLost();
			$this->sqlUpdateBoard();
			return;
		} else if ($this->board[$this->at($x,$y)] != self::HIDDEN_EMPTY) {
			//already clicked...
			return;
		}

		//main show this and adjacent zeroes (used for if you click a 0, it gets adjacent zeroes)
		$search = [[$x,$y],];

		//used to see when to only allow zeroes
		$initSearch = true;

		while (($loc = array_pop($search)) != NULL) {
			//stops infinite reveal loops
			if ($this->at($loc[0],$loc[1])==-1) continue;
			if (!$initSearch && $this->board[$this->at($loc[0],$loc[1])] != self::HIDDEN_EMPTY) continue;

			$numNeighborMines = 0;
			$eightNeighbors = [[-1,-1],[0,-1],[1,-1],[1,0],[1,1],[0,1],[-1,1],[-1,0]];
			foreach ($eightNeighbors as $delta) {
				$coord = $this->at($loc[0]+$delta[0],$loc[1]+$delta[1]);
				if ($coord != -1 && $this->board[$coord] == self::MINE) $numNeighborMines++;
			}
			$this->board[$this->at($loc[0],$loc[1])] = $numNeighborMines;

			if ($numNeighborMines==0) {
				$initSearch = false;
				foreach ($eightNeighbors as $delta) $search[] = [$loc[0]+$delta[0],$loc[1]+$delta[1]];
			}
		}

		//set time end if it just ended
		if (strpos($this->board,"e") === false) $this->setGameToWon();

		$this->sqlUpdateBoard();
	}
	/**
	 * 	Given to user so they can choose updateBoard()
	 */
	public function printSanitizedBoard() {
		if ($this->isWon()) return "DONE";
		if ($this->isLost()) return "DEAD";
		$clean = $this->board;
		//hide where the mines are
		echo str_replace(self::MINE,self::HIDDEN_EMPTY,$clean);
	}
}