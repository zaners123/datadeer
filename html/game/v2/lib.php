<?php

//main creates standardized gameboard format, where

abstract class GameBoard {
	protected $id;
	protected $time_start;
	protected $time_end;
	protected $board;
	/**
	 * Gametype - could make separate table with indices, but for now:
		0 - chess
		1 - checkers
		2 - battleship
	    3 - minesweeper
		4 - sudoku
	*/
	protected $gametype;
	protected $size;

	//main sql variables
	public function getID() {return $this->id;}

	public function __construct() {}

	/**
	 * Fills variable with data from SQL at ID=$id and user=$_SESSION["username"]
	 * @param $id int ID from SQL table
	 */
	public function populateFromID($id) {
		$conn = mysqli_connect("localhost","website",parse_ini_file("/var/www/php/pass.ini")["mysql"],"userdata");
		$query = sprintf(
			"select time_start,time_end,board,gametype,size from game join game_turn using (id) where id=%s and user='%s' and turn=0;",
			mysqli_real_escape_string($conn, $id),
			mysqli_real_escape_string($conn, $_SESSION["username"])
		);
		$result = mysqli_query($conn,$query);
		$result = mysqli_fetch_assoc($result);
		$this->time_start = $result["time_start"];
		$this->time_end = $result["time_end"];
		$this->board = $result["board"];
		$this->gametype = $result["gametype"];
		$this->size = $result["size"];
		//lol this line of code is great, like haha
		$this->id = $id;
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
	public abstract function takeInput();
	/**
	 * 	Given to user so they can choose updateBoard()
	 */
	public abstract function printSanitizedBoard();


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
	/**
	 * Takes board data and inserts it into minesweeper table. Also assigns board an ID (because of autoincrement).
	 */
	public function sqlInsertBoard() {
		$conn = mysqli_connect("localhost","website",parse_ini_file("/var/www/php/pass.ini")["mysql"],"userdata");
		$query = sprintf(
			"insert into game(time_start,board,gametype,size) values (%s,'%s',%s,'%s');",
			mysqli_real_escape_string($conn, $_SERVER["REQUEST_TIME_FLOAT"]),
			mysqli_real_escape_string($conn, $this->board),
			mysqli_real_escape_string($conn, $this->gametype),
			mysqli_real_escape_string($conn, $this->size)
		);
		mysqli_query($conn,$query);
		$this->id = mysqli_insert_id($conn);
		//add current user into group
		mysqli_query($conn,sprintf(
			"set @c = (select count(*) from game_turn where id=%s);",
			mysqli_real_escape_string($conn, $this->id)
		));
		mysqli_query($conn,sprintf(
			"insert into game_turn(id,user,role,turn) values (%s,'%s','%s',@c);",
			mysqli_real_escape_string($conn, $this->id),
			mysqli_real_escape_string($conn, $_SESSION["username"]),
			"owner"
		));
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
			mysqli_real_escape_string($conn, $this->board),
			mysqli_real_escape_string($conn, $this->id)
		);
//		error_log($query);
		mysqli_query($conn,$query);
		//main cycle turn
		mysqli_query($conn,sprintf("set @c = (select count(*) from game_turn where id=%s);",
			mysqli_escape_string($conn,$this->id)
		));
		mysqli_query($conn,sprintf("update game_turn set turn=(turn+1)MOD(@c) where id=%s;",
			mysqli_escape_string($conn,$this->id)
		));
//		error_log(json_encode(mysqli_error_list($conn)));
	}
}