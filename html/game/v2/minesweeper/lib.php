<?php

require "../lib.php";

//standard competition sizes (changeable at any time)
$boardSizes = [[10,10,6,"Practice"],[8,8,10,"Novice"],[16,16,42,"Intermediate"],[30,16,99,"Time Trial"],[30,30,200,"Expert"],[100,100,1400,"The Gauntlet"]];

class MinesweeperBoard extends GameBoard {
	const MINE = 'm';
	const HIDDEN_FLAG = 'f';
	const HIDDEN_EMPTY = 'e';
	//SHOWN_EMPTY = 0 through 8;
//	const SHOWN_EMPTY = 'E';
	//board could also contain number


	//main sql variables


	private $id;
	public function getID() {return $this->id;}
	private $user;
	private $width;
	private $height;
	private $mines;
	//if 0, you win. if -1, you lose, init at width*height-mines
	private $tiles_left;
	//is string of length width*height containing states
	private $board;
	private $time_start;
	private $time_end;


	//main constructors


	public function __construct() {
		parent::__construct("minesweeper");
	}


	/**
	 * Fills variable with data from SQL at ID=$id
	 * @param $id int ID from SQL table
	*/
	public function constructById($id) {
		$conn = mysqli_connect("localhost","website",parse_ini_file("/var/www/php/pass.ini")["mysql"],"userdata");
		$query = sprintf(
			"select * from minesweeper where id=%s and user='%s'",
			mysqli_real_escape_string($conn, $id),
			mysqli_real_escape_string($conn, $_SESSION["username"])
		);
		$result = mysqli_query($conn,$query);
		$result = mysqli_fetch_assoc($result);
		$this->id = $result["id"];
		$this->user = $result["user"];
		$this->width = $result["width"];
		$this->height = $result["height"];
		$this->mines = $result["mines"];
		$this->tiles_left = $result["mines_left"];
		$this->board = $result["board"];
		$this->time_start = $result["time_start"];
		$this->time_end = $result["time_end"];
	}
	private function placeMines($mines) {
		for ($mine=0;$mine<$mines;$mine++) {
			$mineLoc = rand(0,strlen($this->board)-1);
			if ($this->board[$mineLoc] != self::MINE) $this->board[$mineLoc]=self::MINE;
		}
	}
	/**
	 * Generates new board. Also, inserts it into the table.
	 * @param $data array should contain {width, height, mines}
	*/
	public function constructByGenerate($data) {
		if (!isset($data["mines"]) || !isset($data["width"]) || !isset($data["height"])) exit("json must contain width, height, mines");
		$this->mines = $data["mines"];
		$this->width = $data["width"];
		$this->height = $data["height"];
		$this->tiles_left = $this->width*$this->height - $this->mines;
		$this->board = str_pad("",$this->width*$this->height,self::HIDDEN_EMPTY);
		$this->placeMines($this->mines);
		$this->sqlInsertBoard();
	}

	/**
	 * Gets index of (x,y). Is equivalent to (x * width) + y
	 * @return int coord of that character, or -1 if bad index
	*/
	private function at($x,$y) {
		if ($x<0 || $x>=$this->width || $y<0 || $y>=$this->height) return -1;
		return $x * $this->width + $y;
	}

	private function editByClick($x,$y) {
		if ($this->board[$this->at($x,$y)] == self::MINE) {
			$this->tiles_left = -1;
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
			$this->tiles_left--;

			if ($numNeighborMines==0) {
				$initSearch = false;
				foreach ($eightNeighbors as $delta) $search[] = [$loc[0]+$delta[0],$loc[1]+$delta[1]];
			}
		}

		//set time end if it just ended
		$this->time_end = ($this->time_end == 0 && $this->tiles_left==0) ? $_SERVER["REQUEST_TIME_FLOAT"] : 0;
		//testing speed
		//		error_log($this->tiles_left);
	}

	//main user input
	/**
	 * Given coords, it sees if player dies. If not, it reveals the spot and recurses adjacent zeroes
	 * @param $x int x clicked at
	 * @param $y int y clicked at
	 */
	function respondToClick($x, $y) {
		$this->editByClick($x,$y);
		$this->sqlUpdateBoard();
		return;
	}
	/**
	 * 	Given to user so they can choose updateBoard()
	 * @return string user representation of board
	 */
	public function getSanatizedBoard() {
		if ($this->tiles_left== -1) return "DEAD";
		if ($this->tiles_left== 0) return "DONE".($this->time_end-$this->time_start);
		$clean = $this->board;
		//hide where the mines are
		$clean = str_replace(self::MINE,self::HIDDEN_EMPTY,$clean);
		return $clean;
	}

	//main sql storage
	/**
	 * Takes board data and inserts it into minesweeper table. Also assigns board an ID (because of autoincrement).
	 */
	public function sqlInsertBoard() {
		$conn = mysqli_connect("localhost","website",parse_ini_file("/var/www/php/pass.ini")["mysql"],"userdata");
		$query = sprintf(
			"insert into minesweeper(user,width,height,mines,mines_left,board,time_start,time_end) values ('%s',%s,%s,%s,%s,'%s',%s,%s);",
			mysqli_real_escape_string($conn, $_SESSION["username"]),
			mysqli_real_escape_string($conn, $this->width),
			mysqli_real_escape_string($conn, $this->height),
			mysqli_real_escape_string($conn, $this->mines),
			mysqli_real_escape_string($conn, $this->tiles_left),
			mysqli_real_escape_string($conn, $this->board),
			mysqli_real_escape_string($conn, $_SERVER["REQUEST_TIME_FLOAT"]),
			0//end time is zero until end
		);
		mysqli_query($conn,$query);
		$this->id = mysqli_insert_id($conn);
		return $this->id;
	}
	/**
	 * Runs a SQL update off of given board data
	*/
	public function sqlUpdateBoard() {
		$conn = mysqli_connect("localhost","website",parse_ini_file("/var/www/php/pass.ini")["mysql"],"userdata");
		$query = sprintf(
			"update minesweeper set mines_left=%s,board='%s',time_end=%s where id=%s",
			mysqli_real_escape_string($conn, $this->tiles_left),
			mysqli_real_escape_string($conn, $this->board),
			mysqli_real_escape_string($conn, $this->time_end),
			mysqli_real_escape_string($conn, $this->id)
		);
		mysqli_query($conn,$query);
		$this->id = mysqli_insert_id($conn);
		return $this->id;
	}

	public function takeInput($input) {
		$this->respondToClick($input["x"],$input["y"]);
	}
}
/*
abstract class GameBoard {
	protected $data;
	private $tableName;
//	*
//	 * To make an allowed table:
//	 *  Put a table in userdata
//	 *  Give it an (ID int) and a (user varchar(32))
//
	private $allowedTables = array("sudoku","minesweeper");

	//main constructors
	public function __construct($tableName) {
		if (!in_array($tableName, $this->allowedTables)) {
			exit("invalid name");
		}
		$this->tableName = $tableName;
	}

//	*
//	 * Fills variable with data from SQL at ID=$id
//	 * @param $id int ID from SQL table
//
	public function constructById($id) {
		$conn = mysqli_connect("localhost","website",parse_ini_file("/var/www/php/pass.ini")["mysql"],"userdata");
		$query = sprintf(
			"select * from %s where id=%s and user='%s'",
			mysqli_real_escape_string($conn, $this->tableName),
			mysqli_real_escape_string($conn, $id),
			mysqli_real_escape_string($conn, $_SESSION["username"])
		);
		$result = mysqli_query($conn,$query);
		//main data should be parsed by subclasses
		$this->data = mysqli_fetch_assoc($result);
	}
//	*
//	 * Generates new board. Also, inserts it into the table.
//
	public abstract function constructByGenerate($data);

//	*
//	 * An ajax call will pass JSON input data to request.php, which will be fed directly here. PROCESS USER INPUT
//	 * @param $input array JSON data fed by client
//
	public abstract function takeInput($input);
	//main user input
//	*
//	 * 	Given to user so they can choose updateBoard()
//	 * @return string user representation of board
//
	public abstract function getSanatizedBoard();
	//main sql storage
//	*
//	 * Takes board data and inserts it into minesweeper table. Also assigns board an ID (because of autoincrement).
//
	public abstract function sqlInsertBoard();
//	*
//	 * Runs a SQL update off of given board data
//	 * @return int board ID
//
	public abstract function sqlUpdateBoard();
}*/