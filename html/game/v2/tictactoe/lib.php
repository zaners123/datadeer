<?php
require_once "/var/www/html/game/v2/lib.php";

class TicTacToeBoard extends GameBoard {

	const PIECE_X = "X";
	const PIECE_O = "O";
	const PIECE_BLANK = "_";

	public function isPlayerWon($p) {
		return (
			$this->board[0]===$p && $this->board[1]===$p && $this->board[2]===$p ||
			$this->board[3]===$p && $this->board[4]===$p && $this->board[5]===$p ||
			$this->board[6]===$p && $this->board[7]===$p && $this->board[8]===$p ||

			$this->board[0]===$p && $this->board[3]===$p && $this->board[6]===$p ||
			$this->board[1]===$p && $this->board[4]===$p && $this->board[7]===$p ||
			$this->board[2]===$p && $this->board[5]===$p && $this->board[8]===$p ||

			$this->board[0]===$p && $this->board[4]===$p && $this->board[8]===$p ||
			$this->board[2]===$p && $this->board[4]===$p && $this->board[6]===$p
		);
	}
	private function isTied() {
		$ret = true;
		error_log($this->board);
		for($x=0;$x<9;$x++) if ($this->board[$x]===self::PIECE_BLANK[0]) $ret = false;
		return $ret;
	}
	private function getOtherPlayer() {
		return $this->role==self::PIECE_X?self::PIECE_O:self::PIECE_X;
	}
	public function isActive() {
		return !$this->isWon() && !$this->isLost() && !$this->isTied();
	}
	public function isWon() {
		return $this->isPlayerWon($this->role);
	}
	public function isLost() {
		return $this->isPlayerWon($this->getOtherPlayer());
	}

	public function takeInput($input) {
		//stop input when <2 users in game
		$conn = mysqli_connect("localhost","website",parse_ini_file("/var/www/php/pass.ini")["mysql"],"userdata");
		if ($this->getPlayerCount($conn, $this->id)<2) return;
		//stop input if its not your turn
		if (self::getActivePlayer($conn, $this->id) !== $_SESSION["username"]) return;
		//stop input if someone already won
		if (!$this->isActive()) return;
		//take input
		$i = filter_input(INPUT_GET,"i",FILTER_VALIDATE_INT);
		if ($i && $i>=0 && $i<9 && $this->board[$i]==self::PIECE_BLANK) {
			$this->board[$i] = $this->role;
			//after the player moves, the turn is toggled to the next player
			$this->toggleTurn();
			$this->sqlUpdateBoard();
		}
		mysqli_close($conn);
	}

	public function populateByGenerate($size) {
		$this->gametype = self::TICTACTOE;
		$this->size = $size;
		$this->board = str_pad("",9,self::PIECE_BLANK);
		parent::populateByGenerate($size);
	}

	public function getSanitizedBoard()	{
		$state = "";
		if ($this->isWon()) {
			$state = "DONE";
		} else if ($this->isLost()){
			$state = "DEAD";
		} else if ($this->isTied()){
			$state = "TIED";
		}
		$conn = mysqli_connect("localhost","website",parse_ini_file("/var/www/php/pass.ini")["mysql"],"userdata");
		return json_encode(array(
			"state"=>$state,
			"board"=>$this->board,
			"players"=>$this->getPlayerCount($conn, $this->id),
			"active"=>$this->getActivePlayer($conn, $this->id)
		));

	}
}