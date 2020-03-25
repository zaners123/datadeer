<?php
$readOnlyDisabled = true;

require "/var/www/php/requireSignIn.php";

require "boardMaster.php";

if (isset($_GET["gencode"]) && $_GET["gencode"] === "true") {
    $newId="f";//all chess game IDs start with a c, checkers should start with an x, connect with f
    $characters = array_merge(range('a','z'), range('0','9'));
    $max = count($characters)-1;
    for ($i = 0; $i < 5; $i++) {
        //if ($i==5 || $i == 10) $str.="-";
        $rand = mt_rand(0, $max);
        $newId .= $characters[$rand];
    }
    $board = array_merge(
        array($_SESSION["username"],true),//the board starts with a user, and the fact that it is their turn. Once they go, the turn bit flips.
        array_fill(0,42,0)//the middle gap
    );
    saveBoard($newId, $board);
    echo $newId;
    return;
} else if (isset($_GET["id"])) {
    if (isset($_GET["move"])) {
        echo makeMove($_GET["id"], $_GET["move"]);
    } else {
        echo loadBoard($_GET["id"]);
    }
}

function isOwner($board) {
    return ($board[0]===$_SESSION["username"]);
}

/**
 *  Piece Map:
 *      owner is 1 and alt is 2
 *      0 - Empty
 *      1 - owner / white
 *      2 - alt / black
 */
function makeMove($id, $move) {
    //request move would be "?move=0" to drop in col 0 / first column
    if (strlen($move) != 1) return "no0";
    $board = json_decode(loadBoard($id));
    //check that it's their turn
    // testing remove turn-checker!
    if (isOwner($board)!==$board[1]) return "no0b";

    // Formats $move into an x and a y
    $col = intval($move);
    $colOfs = 2+$col;
    /*$fromx = intdiv($intmove, 1000);// 1234/1000 = 1
    $fromy = intdiv($intmove, 100) - $fromx*10;// 1234/100 - 10 = 2
    $toy = $intmove % 10;// 1234%10 = 4
    $tox = ($intmove % 100 - $toy) / 10;
    $from = toIndex($fromx, $fromy);
    $to = toIndex($tox, $toy);*/

    // if moving to full row say no (board[col] would be first row)
    if ($board[$colOfs] != 0) return "no1";

    //if off the board say no
    if ($col < 0 || $col >= 7) return "no2";

    // drop the piece (first clear y is placement)
    $dropLoc = -1;
    for ($y=5;$y>=0;$y--) {
        if ($board[$colOfs+($y*7)] == 0) {
            $dropLoc = $colOfs+($y*7);
            //return "no y=".$y."dropLoc=".$dropLoc;
            break;
        }
    }
    if ($dropLoc===-1) return "no3";
    $dropType = isOwner($board)?1:2;
    $board[$dropLoc] = $dropType;

    //winning logic
    //if sum of same pieces to left of it plus sum of same pieces to the right is 3, its good
    //same for up-down, and both diagonals.
    $directions = array(1,6,7,8);
    foreach ($directions as $direction) {
        $sum = 0;//object to get to four


        //iterate in positive directions (right, up, etc)
        for ($loc=$dropLoc+$direction ; $loc < $dropLoc + 4*$direction ; $loc+=$direction) {

            if ($board[$loc] == $dropType) {
                //echo "noloc".$loc;
                $sum++;
            } else {
                break;
            }
            if (aboutToFall($loc,$direction)) break;
        }


        //iterate in negative directions (left, down, etc)
        for ($loc=$dropLoc-$direction ; $loc > $dropLoc - 4*$direction ; $loc-=$direction) {

            if ($board[$loc] == $dropType) {
                //echo "noloc".$loc;
                $sum++;
            } else {
                break;
            }

            if (aboutToFall($loc,-$direction)) break;
        }

        //return "no Sum:".$sum;
        //if sum is increased by 3 due to adjacent pieces
        if ($sum >= 3) {
            if ($dropType==1) {
                //owner won
                $board[1]="WWIN";
	            ended($id);
	            break;
            } else if ($dropType==2) {
                //alt won
                $board[1]="BWIN";
	            ended($id);
	            break;
            }
        }
    }

    //cycle the turn (only if noone has won yet)
    $board[1] = ($board[1]==="BWIN"||$board[1]==="WWIN")?$board[1]:!$board[1];

    // actually move the piece (but change it to a king if its to a end row)

    // save the board
    saveBoard($id, $board);
    return json_encode($board);
}

//function used if this line of win should not be followed (going off board, loop from left to right of board, etc.)
function aboutToFall($loc, $direction) {
    //if going through top of bottom wall
    if (($loc < 9 && $direction<0) || ($loc > 36 && $direction>0)) return true;
    //if going through left or right wall
    if (($loc % 7 == 2 && $direction<0) || ($loc % 7 == 1 && $direction>0)) return true;
    //line can keep going
    return false;
}
