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
	const CONNECT = 6;
	const POKER = 7;
	const FOLDERS = [
		3 => "minesweeper",
		4 => "sudoku",
		5 => "tictactoe",
		7 => "poker",
	];
	protected $size;
	//the current player's role
	protected $role;

	//main sql variables
	public function getID() {
		return $this->id;
	}

	public function __construct() {
	}

	public static function getGameType($conn, $id) {
		//add user to game_turn list (if they're already on it oh well)
		$query = sprintf(
			"select gametype as g from game where id=%s;",
			mysqli_real_escape_string($conn, $id)
		);
		return mysqli_fetch_assoc(mysqli_query($conn, $query))["g"];
	}

	public static function isInGame($conn, $id, $user) {
		$query = sprintf(
			"select user as u from game join game_turn using (id) where id=%s and user='%s';",
			mysqli_real_escape_string($conn, $id),
			mysqli_real_escape_string($conn, $user)
		);
		return mysqli_num_rows(mysqli_query($conn, $query)) > 0;
	}

	protected static function getPlayerList($conn, $id) {
		$query = sprintf(
			"select user as c from game join game_turn using (id) where id=%s order by turn;",
			mysqli_real_escape_string($conn, $id)
		);
		$res = mysqli_query($conn, $query);
		$ret = [];
		while ($row = mysqli_fetch_assoc($res)) {
			//ret row, shaggy
			$ret[] = $row["c"];
		}
		return $ret;
	}

	protected static function getNumPlayers($conn, $id) {
		$query = sprintf(
			"select count(*) as c from game join game_turn using (id) where id=%s;",
			mysqli_real_escape_string($conn, $id)
		);
		return mysqli_fetch_assoc(mysqli_query($conn, $query))["c"];
	}

	public static function letPlayerJoinGame($conn, $id, $role, $players) {
		//add player's role and name to the board slot, if necessary
		$query = sprintf(
			"insert into game_turn(id,user,role,turn) values (%s,'%s','%s','%s');",
			mysqli_real_escape_string($conn, $id),
			mysqli_real_escape_string($conn, $_SESSION["username"]),
			mysqli_real_escape_string($conn,$role),
			mysqli_real_escape_string($conn,$players)
		);
		return true == mysqli_query($conn, $query);
	}

	/**
	 * When overriding, follow the general structure of GameBoard::playerWantsToJoinGame function
	 * @param $players int number of players
	 * @return bool|string Returns role if player can join, or false if they can't
	 */
	public function canPlayerJoinGame($conn, $players) {
		//add them to the game
		if ($players >= 1) return false;
		return "singleplayer";
	}

	/**
	 * Fills variable with data from SQL at ID=$id and user=$_SESSION["username"]
	 * @param $id int ID from SQL table
	 */
	public function populateFromID($id) {
		//This function could also be called "Join Game"
		$conn = mysqli_connect("localhost", "website", parse_ini_file("/var/www/php/pass.ini")["mysql"], "userdata");

		$isInGame = self::isInGame($conn, $id, $_SESSION["username"]);
		if (!$isInGame) {
			$players = $this->getNumPlayers($conn, $id);
			$role = $this->canPlayerJoinGame($conn, $players);
//			error_log("ROLE == ".$role);
			if ($role) {
				self::letPlayerJoinGame($conn, $id, $role, $players);
			} else {
				exit("GAME FULL");
			}
		}

		//find the board
		$query = sprintf(
			"select time_start,time_end,board,gametype,size,role from game join game_turn using (id) where id=%s and user='%s';",
			mysqli_real_escape_string($conn, $id),
			mysqli_real_escape_string($conn, $_SESSION["username"])
		);
		//		error_log($query);
		$result = mysqli_query($conn, $query);
		$result = mysqli_fetch_assoc($result);
		$this->time_start = $result["time_start"];
		$this->time_end = $result["time_end"];
		$this->board = json_decode($result["board"], true);
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
	public abstract function populateByGenerate($size);
	//		$this->size = $size;
	//		$this->sqlInsertBoard();
	//main user input/output
	/**
	 * An ajax call will pass JSON input data to request.php, which will be fed directly here. PROCESS USER INPUT
	 * main remember to call $this->sqlUpdateBoard();
	 * main uses $_GET to pass input
	 */
	public abstract function takeInput();

	/**
	 *    Given to user so they can choose updateBoard()
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
		$this->time_end = -1;
	}

	public function isWon() {
		return (!$this->isLost() && !$this->isActive());
	}

	public function isLost() {
		return $this->time_end == self::LOST;
	}

	public function isActive() {
		return $this->time_end == self::ACTIVE;
	}

	//main sql storage

	protected static function getActivePlayer($conn, $id) {
		return self::getPlayerByTurnNumber($conn, $id, 0);
	}

	protected static function getPlayerByTurnNumber($conn, $id, $turn) {
		$turn = $turn % self::getNumPlayers($conn, $id);
		$query = sprintf(
			"select user from game_turn where id=%s and turn=%s;",
			mysqli_real_escape_string($conn, $id),
			mysqli_real_escape_string($conn, $turn)
		);
//		error_log($query);
		return mysqli_fetch_assoc(mysqli_query($conn, $query))["user"];
	}

	public static function getPlayerTurnNumber($conn, $id, $user) {
		return mysqli_fetch_assoc(mysqli_query($conn, sprintf(
			"select turn from game_turn where id=%s and user='%s';",
			mysqli_real_escape_string($conn, $id),
			mysqli_real_escape_string($conn, $user)
		)))["turn"];
	}

	protected static function kickPlayer($conn, $id, $user) {
		echo "KICKED";
		mysqli_query($conn, sprintf("delete from game_turn where id=%s and user='%s';",
			mysqli_escape_string($conn, $id),
			mysqli_escape_string($conn, $user)
		));
	}

	protected function toggleTurn($conn) {
		//main cycle turn
		$players = $this->getNumPlayers($conn, $this->id);
		mysqli_query($conn, sprintf("update game_turn set turn=(turn+1)MOD(%s) where id=%s;",
			mysqli_escape_string($conn, $players),
			mysqli_escape_string($conn, $this->id)
		));
	}

	/**
	 * Takes board data and inserts it into minesweeper table. Also assigns board an ID (because of autoincrement).
	 */
	public function sqlInsertBoard() {
		$conn = mysqli_connect("localhost", "website", parse_ini_file("/var/www/php/pass.ini")["mysql"], "userdata");
		$query = sprintf(
			"insert into game(time_start,board,gametype,size) values (%s,'%s',%s,'%s');",
			mysqli_real_escape_string($conn, $_SERVER["REQUEST_TIME_FLOAT"]),
			mysqli_real_escape_string($conn, json_encode($this->board)),
			mysqli_real_escape_string($conn, $this->gametype),
			mysqli_real_escape_string($conn, $this->size)
		);
		mysqli_query($conn, $query);
		$this->id = mysqli_insert_id($conn);
		//		error_log(json_encode(mysqli_error_list($conn)));
		return $this->id;
	}

	/**
	 * Runs a SQL update off of given board data
	 */
	public function sqlUpdateBoard() {
		$conn = mysqli_connect("localhost", "website", parse_ini_file("/var/www/php/pass.ini")["mysql"], "userdata");
		if (!$this->time_end) $this->time_end = 0;
		$query = sprintf(
			"update game set time_end=%s,board='%s' where id=%s",
			mysqli_real_escape_string($conn, $this->time_end),
			mysqli_real_escape_string($conn, json_encode($this->board)),
			mysqli_real_escape_string($conn, $this->id)
		);
		//		error_log($query);
		mysqli_query($conn, $query);
	}
}