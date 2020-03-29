<?php

//main creates standardized gameboard format, where

abstract class GameBoard {
	protected $id;
	protected $time_start;
	protected $time_end;
	protected $board;


	protected $gametype;
	const CHESS = 0;
	const CHECKERS = 1;
	const BATTLESHIP = 2;
	const MINESWEEPER = 3;
	const SUDOKU = 4;
	const TICTACTOE = 5;
	const FOLDERS = [
		3=>"minesweeper",
		4=>"sudoku",
		5=>"tictactoe",
	];
	protected $size;
	//the current player's role
	protected $role;

	//main sql variables
	public function getID() {return $this->id;}

	public function __construct() {}

	public static function getGameType($conn, $id) {
		//add user to game_turn list (if they're already on it oh well)
		$query = sprintf(
			"select gametype as g from game where id=%s;",
			mysqli_real_escape_string($conn, $id)
		);
		return mysqli_fetch_assoc(mysqli_query($conn,$query))["g"];
	}

	public static function isInGame($conn, $id, $user) {
		$query = sprintf(
			"select user as u from game join game_turn using (id) where id=%s and user='%s';",
			mysqli_real_escape_string($conn, $id),
			mysqli_real_escape_string($conn,$user)
		);
		return mysqli_num_rows(mysqli_query($conn,$query)) > 0;
	}

	protected static function getPlayerCount($conn, $id) {
		$query = sprintf(
			"select count(*) as c from game join game_turn using (id) where id=%s;",
			mysqli_real_escape_string($conn, $id)
		);
		return mysqli_fetch_assoc(mysqli_query($conn,$query))["c"];
	}

	protected static function getActivePlayer($conn, $id) {
		$query = sprintf(
			"select user from game_turn where id=%s and turn=0;",
			mysqli_real_escape_string($conn, $id)
		);
		return mysqli_fetch_assoc(mysqli_query($conn,$query))["user"];
	}

	/**
	 * Fills variable with data from SQL at ID=$id and user=$_SESSION["username"]
	 * @param $id int ID from SQL table
	 */
	public function populateFromID($id) {
		//This function could also be called "Join Game"
		$conn = mysqli_connect("localhost","website",parse_ini_file("/var/www/php/pass.ini")["mysql"],"userdata");
		$gametype = $this->getGameType($conn, $id);

		$isInGame = self::isInGame($conn, $id, $_SESSION["username"]);
		if (!$isInGame) {
			//add them to the game
			$players = $this->getPlayerCount($conn, $id);
			switch ($gametype) {
				case self::TICTACTOE:
//					error_log($gametype);
//					error_log($players);
					if ($players>=2) exit("GAME FULL");
					$role = $players==0?"X":"O";
					break;
				default://for single-player games
					if ($players>=1) exit("GAME FULL");
					$role = "singleplayer";
			}

			//add player's role and name to the board slot, if necessary
			$query = sprintf(
				"insert into game_turn(id,user,role,turn) values (%s,'%s','%s','%s');",
				mysqli_real_escape_string($conn, $id),
				mysqli_real_escape_string($conn, $_SESSION["username"]),
				$role,
				$players
			);
			mysqli_query($conn,$query);
//			error_log($query);
		}

		//find the board
		$query = sprintf(
			"select time_start,time_end,board,gametype,size,role from game join game_turn using (id) where id=%s and user='%s';",
			mysqli_real_escape_string($conn, $id),
			mysqli_real_escape_string($conn, $_SESSION["username"])
		);
//		error_log($query);
		$result = mysqli_query($conn,$query);
		$result = mysqli_fetch_assoc($result);
		$this->time_start = $result["time_start"];
		$this->time_end = $result["time_end"];
		$this->board = json_decode($result["board"],true);
		$this->gametype = $result["gametype"];
		$this->size = $result["size"];
		$this->role = $result["role"];
		$this->id = $id;
		mysqli_close($conn);
//		error_log(json_encode(mysqli_error_list($conn)));
	}
	/**
	 * Generates new board. Do this by first validating the size, then setting the size, then setting everything else
	 * make sure to call parent. SET SIZE AT START so you can use parseSize in the generate function (necessary because that's the only real use of the size function)
	 */
	public function populateByGenerate($size) {
		$this->size = $size;
		$this->sqlInsertBoard();
	}
	//main user input/output
	/**
	 * An ajax call will pass JSON input data to request.php, which will be fed directly here. PROCESS USER INPUT
	 * main remember to call $this->sqlUpdateBoard();
	 * main uses $_GET to pass input
	 */
	public abstract function takeInput($input);
	/**
	 * 	Given to user so they can choose updateBoard()
	 */
	public abstract function getSanitizedBoard();


	const LOST = -1;
	const ACTIVE = 0;
	/**
	 * Should be conditionally called in takeInput, if they won
	 */
	public function setGameToWon() {
		if (!$this->time_end) {
			$this->time_end = $_SERVER["REQUEST_TIME_FLOAT"];
		}
	}
	public function setGameToLost() {
		$this->time_end=-1;
	}
	public function isWon() {
		return (!$this->isLost() && !$this->isActive());
	}
	public function isLost() {
		return $this->time_end==self::LOST;
	}
	public function isActive() {
		return $this->time_end==self::ACTIVE;
	}
	//main sql storage

	protected function toggleTurn() {
		$conn = mysqli_connect("localhost","website",parse_ini_file("/var/www/php/pass.ini")["mysql"],"userdata");
		//main cycle turn
		mysqli_query($conn,sprintf("set @c = (select count(*) from game_turn where id=%s);",
			mysqli_escape_string($conn,$this->id)
		));
		mysqli_query($conn,sprintf("update game_turn set turn=(turn+1)MOD(@c) where id=%s;",
			mysqli_escape_string($conn,$this->id)
		));
//		error_log(json_encode(mysqli_error_list($conn)));
	}

	/**
	 * Takes board data and inserts it into minesweeper table. Also assigns board an ID (because of autoincrement).
	 */
	public function sqlInsertBoard() {
		$conn = mysqli_connect("localhost","website",parse_ini_file("/var/www/php/pass.ini")["mysql"],"userdata");
		$query = sprintf(
			"insert into game(time_start,board,gametype,size) values (%s,'%s',%s,'%s');",
			mysqli_real_escape_string($conn, $_SERVER["REQUEST_TIME_FLOAT"]),
			mysqli_real_escape_string($conn, json_encode($this->board)),
			mysqli_real_escape_string($conn, $this->gametype),
			mysqli_real_escape_string($conn, $this->size)
		);
		mysqli_query($conn,$query);
		$this->id = mysqli_insert_id($conn);
//		error_log(json_encode(mysqli_error_list($conn)));
		return $this->id;
	}
	/**
	 * Runs a SQL update off of given board data
	 */
	public function sqlUpdateBoard() {
		$conn = mysqli_connect("localhost","website",parse_ini_file("/var/www/php/pass.ini")["mysql"],"userdata");
		if (!$this->time_end) $this->time_end=0;
		$query = sprintf(
			"update game set time_end=%s,board='%s' where id=%s",
			mysqli_real_escape_string($conn, $this->time_end),
			mysqli_real_escape_string($conn, json_encode($this->board)),
			mysqli_real_escape_string($conn, $this->id)
		);
//		error_log($query);
		mysqli_query($conn,$query);
	}
}