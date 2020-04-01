<?php
class PokerBoard extends GameBoard {

	public function takeInput($input) {
		
	}

	public function getSanitizedBoard()	{

	}

	public function populateByGenerate($size) {
		$this->size = $size;
		$this->sqlInsertBoard();
	}
}