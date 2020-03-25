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


	//main constructors


	public function __construct() {}
	/**
	 * Fills variable with data from SQL at ID=$id
	 * @param $id int ID from SQL table
	 */
	public function constructById($id) {
		$conn = mysqli_connect("localhost","website",parse_ini_file("/var/www/php/pass.ini")["mysql"],"userdata");
		$query = sprintf(
			"select time_start,time_end,board,gametype,size from game where id=%s and user='%s'",
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
		$this->__construct();
	}
	/**
	 * Generates new board. When overriding, call at end
	 */
	public function constructByGenerate($size) {
		$this->sqlInsertBoard();
		$this->__construct();
	}


	//main user input/output


	/**
	 * An ajax call will pass JSON input data to request.php, which will be fed directly here. PROCESS USER INPUT
	 * @param $input array JSON data fed by client
	 */
	public function takeInput($input) {

		$this->sqlUpdateBoard();
	}
	/**
	 * 	Given to user so they can choose updateBoard()
	 * @return string user representation of board
	 */
	public abstract function getSanatizedBoard();

	/**
	 * Should be conditionally called in takeInput, if they won
	 */
	public function setGameToWon() {
		$time_end = $_SERVER["REQUEST_TIME_FLOAT"];
	}

	//main sql storage

	/**
	 * Takes board data and inserts it into minesweeper table. Also assigns board an ID (because of autoincrement).
	 */
	public function sqlInsertBoard() {
		$conn = mysqli_connect("localhost","website",parse_ini_file("/var/www/php/pass.ini")["mysql"],"userdata");
		$query = sprintf(
			"insert into game(user,time_start,board,gametype,size) values ('%s',%s,'%s',%s,'%s');",
			mysqli_real_escape_string($conn, $_SESSION["username"]),
			mysqli_real_escape_string($conn, $_SERVER["REQUEST_TIME_FLOAT"]),
			mysqli_real_escape_string($conn, $this->board),
			mysqli_real_escape_string($conn, $this->gametype),
			mysqli_real_escape_string($conn, $this->size)
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
			"update game set time_end=%s,board='%s' where id=%s and user='%s'",
			mysqli_real_escape_string($conn, $this->time_end),
			mysqli_real_escape_string($conn, $this->board),
			mysqli_real_escape_string($conn, $this->id),
			mysqli_real_escape_string($conn, $_SESSION["username"])
		);
		mysqli_query($conn,$query);
	}
}