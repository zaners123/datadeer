<?php
$readOnlyDisabled = true;
require "/var/www/php/requireSignIn.php";

require "boardMaster.php";

if (isset($_GET["gencode"]) && $_GET["gencode"] === "true") {
    $newId="x";//all chess game IDs start with a c, checkers should start with an x
    $characters = array_merge(range('a','z'), range('0','9'));
    $max = count($characters)-1;
    for ($i = 0; $i < 5; $i++) {
        //if ($i==5 || $i == 10) $str.="-";
        $rand = mt_rand(0, $max);
        $newId .= $characters[$rand];
    }
    $board = array_merge(
        array($_SESSION["username"],true),//the board starts with a user, and the fact that it is their turn. Once they go, the turn bit flips.
        array(0,1,0,1,0,1,0,1),//the top pieces
        array(1,0,1,0,1,0,1,0),//the top pieces
        array(0,1,0,1,0,1,0,1),//the top pieces
        array_fill(0,16,0),//the middle gap
        array(3,0,3,0,3,0,3,0),//the bottom pieces
        array(0,3,0,3,0,3,0,3),//the bottom pieces
        array(3,0,3,0,3,0,3,0)//the bottom pieces
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
 *      owner is 1,2 and alt is 3,4
 *      0 - Empty
 *      1 - Single White
 *      2 - Double White
 *      3 - Single Black
 *      4 - Double Black
 */
function makeMove($id, $move) {
    //request move would be "?move=0055" to move a piece from "A1" to "E6"
    if (strlen($move) != 4) return "no0";
    $board = json_decode(loadBoard($id));
    //check that it's their turn
    //testing remove turn-checker!
    if (isOwner($board)!==$board[1]) return "no0b";

    // Formats $move into an x and a y
    $intmove = intval($move);
    $fromx = intdiv($intmove, 1000);// 1234/1000 = 1
    $fromy = intdiv($intmove, 100) - $fromx*10;// 1234/100 - 10 = 2
    $toy = $intmove % 10;// 1234%10 = 4
    $tox = ($intmove % 100 - $toy) / 10;
    $from = toIndex($fromx, $fromy);
    $to = toIndex($tox, $toy);

    // if moving no piece say no
    if ($board[$from] == 0) return "no1";

    //if moving to not empty spot say no
    if ($board[$to]!=0) return "no1a";

    //if moving on wrong color say no
    if (($tox+$toy)%2==0) return "no1b";

    // if moving to same location say no
    if ($fromx===$tox && $fromy===$toy) return "no2";

    // if it's off the board say no
    if (!isOnBoard($from) || !isOnBoard($to)) return "no3";

    // if trying to move other person's piece, return no -- the owner is pieces 1-2, the alt is 3-4
    if (isOwner($board) === ($board[$from] > 2)) return "no5";

    // if a piece is being moved wrong say no
    $canDown = $board[$from]==1 || $board[$from]==2 || $board[$from]==4;
    $canUp = $board[$from]==2 || $board[$from]==3 || $board[$from]==4;

    //there are two types of moves, a single "slide" and a multi-"jump"

    if (!validSlide($canDown, $canUp, $fromx, $fromy, $tox, $toy)) {
        $jumpRoute = validJump($canDown, $canUp, $board, $fromx, $fromy, $from, $tox, $toy, $to);
        if (is_null($jumpRoute)) {
            return "no6";
        } else {
            //take the pieces
            //error_log("gonna take ".implode("|",$jumpRoute));
            for($i=0,$size=count($jumpRoute)-1;$i<$size;++$i) {
                $take = ($jumpRoute[$i]+$jumpRoute[$i+1])/2;
                $board[$take] = 0;
            }
        }
    }

    //cycle the turn (only if noone has won yet)
    $board[1] = ($board[1]==="BWIN"||$board[1]==="WWIN")?$board[1]:!$board[1];

    // actually move the piece (but change it to a king if its to a end row)
    if ($toy===0 || $toy===7) {
        if ($board[$from]===1) {
            //castle white
            $board[$to]=2;
        } else if ($board[$from]===3) {
            //castle black
            $board[$to]=4;
        } else {
            //castle moving to castle row again
            $board[$to]=$board[$from];
        }
    } else {
        $board[$to]=$board[$from];
    }
    $board[$from]=0;

    // save the board
    saveBoard($id, $board);
    return json_encode($board);
}
function validSlide($canDown, $canUp, $fromx, $fromy, $tox, $toy) {
    //return true;
    //moving one in the right direction
    if (($canDown && $toy==$fromy+1) || ($canUp && $toy==$fromy-1)) {
        //moving one to the left or right
        if ($tox == $fromx + 1 || $tox == $fromx - 1) {
            //valid slide
            return true;
        }
    }
    return false;
}

/**The code for seeing if the piece can jump from a location to somewhere else.

 @return null if it cannot jump there
 @return the route it would take if it can jump there

 */
function validJump($canDown, $canUp, $board, $fromx, $fromy, $from, $tox, $toy, $to) {

    //type of piece that is moving
    $pieceType = $board[$from];

    //make array of all possible moves, then see if this is in the array
    $jumpStarts = array(array($from));

    //log where its going from/to in general
    //error_log("start".$fromx.",".$fromy."to".$tox.",".$toy);

    $relMove = array();
    if ($canDown) {
        $relMove[] = 7;
        $relMove[] = 9;
    }
    if ($canUp) {
        $relMove[] = -7;
        $relMove[] = -9;
    }

    $halt = 12;
    //keep predicting possibilities
    while ($halt-- > 0) {
        //jumpStartArr is an array holding where the piece has jumped (necessary to know what pieces to take)
        //for every breadth-able location
        foreach ($jumpStarts as $jumpHistoryKey => $jumpHistory) {
            $jumpingFrom = $jumpHistory[0];
            //for every possible route (BREADTH FIRST)
            //top left, top right, bottom left, bottom right
            foreach ($relMove as $moveDelta) {
                //if you are jumping over the opponents piece add it as a possible move
                if (isOnBoard($jumpingFrom+$moveDelta) && isOtherTeam($pieceType,$board[$jumpingFrom+$moveDelta])) {
                    //error_log("trying from ".$jumpingFrom." to ".($jumpingFrom+$moveDelta*2));
                    //new path is where the piece goes throughout it's journey, including the last hop
                    $newHistory = array_merge(array($jumpingFrom+$moveDelta*2),$jumpHistory);
                    //if you are at the destination return the route
                    if ($jumpingFrom+$moveDelta*2===$to) return $newHistory;
                    //if you are potentially on your way add a possible route to iterate
                    $jumpStarts[] = $newHistory;
                }
            }
            //removes searched branch
            unset($jumpStarts[$jumpHistoryKey]);
        }
        //if nothing left to search finish
        if (sizeof($jumpStarts)===0) break;
    }

    //no way the jump could work or reach
    return null;
}

function isOnBoard($loc) {
    return $loc >= 2 && $loc < 66;
}

function isOtherTeam($boardFrom, $boardTo) {
    // if you're taking a piece and it's on your side say no
    if ($boardFrom==0 || $boardTo==0) return false;
    return ($boardFrom>2)!=($boardTo>2);
}

function toIndex($boardx, $boardy) {
    return 2 + $boardy*8 + $boardx;
}