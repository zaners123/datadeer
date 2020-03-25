<?php
//main returns and receives game info
require_once "/var/www/php/couch.php";




//main give them all games (reason==get)
if ($_GET["match"]==="g") {
	echo json_encode(getDoc("gamematch","games",$blankDefault));
}
if (isset($_GET["gen"])) {
	//main generate board, then add board to game list, then return ID

	//generate board and get ID
	$_GET["gencode"] = "true";
	ob_start();
	if ($_GET["gen"]==="battleship") include "battleshipBase.php";
	if ($_GET["gen"]==="checkers") include "checkersBase.php";
	if ($_GET["gen"]==="chess") include "chessBase.php";
	if ($_GET["gen"]==="connect") include "connectBase.php";
	$gameId = ob_get_clean();

	//add board to game list
	$doc = getDoc("gamematch","games",$blankDefault);
	$doc[$_SESSION["username"]] = $gameId;
	setDoc("gamematch","games",$doc);
	//return id
	echo $gameId;
}