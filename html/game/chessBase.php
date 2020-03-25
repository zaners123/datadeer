<?php
$readOnlyDisabled = true;

require "/var/www/php/requireSignIn.php";

require "boardMaster.php";

if (isset($_GET["gencode"]) && $_GET["gencode"] === "true") {
    $newId="c";//all chess game IDs start with a c, checkers should start with an x
    $characters = array_merge(range('a','z'), range('0','9'));
    $max = count($characters)-1;
    for ($i = 0; $i < 5; $i++) {
        //if ($i==5 || $i == 10) $str.="-";
        $rand = mt_rand(0, $max);
        $newId .= $characters[$rand];
    }
    $board = array_merge(
        array($_SESSION["username"],true),//the board starts with a user, and the fact that it is their turn. Once they go, the turn bit flips.
        array(1,2,3,5,4,3,2,1),//the top pieces
        array_fill(0,8,6),//the top pawns
        array_fill(0,32,0),//the middle gap
        array_fill(0,8,12),//the bottom pawns
        array(7,8,9,11,10,9,8,7)//the bottom pieces
    );
    saveBoard($newId, $board);
    //TODO make sure that key doesn't already exist (generating the same key twice would wipe a board)
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
function makeMove($id, $move) {
    //request move would be "?move=0055" to move a piece from "A1" to "E6"
    //TODO if moving a pawn into the final row, have the move request be 0123#Q where Q is Queen, K is Knight/Horse, R is Rook, B is bishop
    if (strlen($move) != 4) return "no0";
    $board = json_decode(loadBoard($id));
    //check that it's their turn
    if (isOwner($board)!==$board[1]) return "no0b";

    // Formats $move into an x and a y
    $intmove = intval($move);
    $fromx = intdiv($intmove, 1000);// 1234/1000 = 1
    $fromy = intdiv($intmove, 100) - $fromx*10;// 1234/100 - 10 = 2
    $toy = $intmove % 10;// 1234%10 = 4
    $tox = ($intmove % 100 - $toy) / 10;
    $from = toIndex($fromx, $fromy);
    $to = toIndex($tox, $toy);
    // if no piece return no
    if ($board[$from] === 0) return "no1";
    // if moving to same location say no
    if ($fromx===$tox && $fromy===$toy) return "no2";
    // if it's off the board say no
    if ($from < 2 || $from >= 66 || $to < 2 || $to >= 66) return "no3";

    // if trying to move other person's piece, return no -- the owner is pieces 1-6, the alt is 7-12
    if (isOwner($board) === ($board[$from] > 6)) return "no5";

    // if you're taking a piece and it's on your side say no (unless you're castling)
    if ($board[$to]!==0 && (($board[$from]>6)===($board[$to]>6))) {
        // if you are taking your rook with your king, swap them.
        // white moving king from original location
        //echo "nostart".$board[$to].",".$to;
        if (1==$board[$to] && 5==$board[$from] && 5==$from) {
            //echo"AAA";
            // moving to their rook
            if ($to==2 && $board[3]==0 && $board[4]==0) {
                //swap from 5 to 2

                $board[$to] = 0;
                $board[$from] = 0;
                $board[$to+1] = 5;
                $board[$from-1] = 1;

                //save and end turn
                $board[1] = ($board[1]==="BWIN"||$board[1]==="WWIN")?$board[1]:!$board[1];
                saveBoard($id, $board);
                return json_encode($board);
            } else if ($to==9 && $board[6]==0 && $board[7]==0 && $board[8]==0) {
                //swap from 5 to 9

                $board[$to] = 0;
                $board[$from] = 0;
                $board[$to-2] = 5;
                $board[$from+1] = 1;

                //save and end turn
                $board[1] = ($board[1]==="BWIN"||$board[1]==="WWIN")?$board[1]:!$board[1];
                saveBoard($id, $board);
                return json_encode($board);

            }
        // black king moving from original location
        } else if (7==$board[$to] && 11==$board[$from] && 61==$from) {
            // moving to their rook
            if ($to==58 && $board[59]==0 && $board[60]==0) {
                //swap from 61 to 58
                $board[$to] = 0;
                $board[$from] = 0;
                $board[$to+1] = 11;
                $board[$from+1] = 7;
                //save and end turn
                $board[1] = ($board[1]==="BWIN"||$board[1]==="WWIN")?$board[1]:!$board[1];
                saveBoard($id, $board);
                return json_encode($board);
            } else if ($to==65 && $board[64]==0 && $board[63]==0 && $board[62]==0) {
                //swap from 61 to 65
                $board[$to] = 0;
                $board[$from] = 0;
                $board[$to-1] = 11;
                $board[$from+1] = 7;
                //save and end turn
                $board[1] = ($board[1]==="BWIN"||$board[1]==="WWIN")?$board[1]:!$board[1];
                saveBoard($id, $board);
                return json_encode($board);

            }
        }
        return "no4";
    }

    // if a piece is being moved wrong say no
    switch ($board[$from]) {
        case 1:case 7://rook -- if x different and y different return no
            if (!($fromx===$tox || $fromy===$toy) ||
                isPieceBetweenOrthogonal($board, $fromx, $fromy, $tox, $toy)
            ) return "no6";break;
        case 2:case 8://horse / knight
            if (!(
                (abs($fromx-$tox)===2 && abs($fromy-$toy)===1) ||//x change two and y change one
                (abs($fromx-$tox)===1 && abs($fromy-$toy)===2)//x change one and y change two
            )) return "no7";break;
        case 3:case 9://bishop -- if x change different than y change return no
            if (abs($fromx-$tox)!==abs($fromy-$toy) ||
                isPieceBetweenDiagonal($board, $from, $fromx, $fromy, $to, $tox, $toy)) return "no8";break;
        case 4:case 10://queen -- if rook bad and bishop bad return no
            if (
                (
                    (!($fromx===$tox || $fromy===$toy)) ||
                    isPieceBetweenOrthogonal($board, $fromx, $fromy, $tox, $toy)
                ) && (
                    abs($fromx-$tox)!==abs($fromy-$toy) ||
                    isPieceBetweenDiagonal($board, $from, $fromx, $fromy, $to, $tox, $toy)
                )
            ) return "no9";break;
        case 5:case 11://king -- if x difference > 1 or y difference > 1 return no
            if (abs($fromx-$tox)>1 || abs($fromy-$toy)>1) return "no10";break;
        case 6://down going pawn
            if (!(
                ($tox===$fromx && $fromy===1 && $toy===$fromy+2) ||//pawn two-move
                ($tox===$fromx && $toy===$fromy+1 && $board[$to]===0) ||//pawn one-move moving forward into a blank square
                (abs($tox-$fromx)===1 && $toy===$fromy+1 && $board[$to]!==0)//x difference is one and it is attacking
            )) return "no11";break;
        case 12://up going pawn
            if (!(
                ($tox===$fromx && $fromy===6 && $toy===$fromy-2) ||//pawn two-move
                ($tox===$fromx && $toy===$fromy-1 && $board[$to]===0) ||//pawn one-move moving forward into a blank square
                (abs($tox-$fromx)===1 && $toy===$fromy-1 && $board[$to]!==0)//x difference is one and it is attacking
            )) return "no12";break;
    }

    // if you take a king you win
    if ($board[$to]===11) {
	    ended($id);
	    $board[1]="WWIN";
    }
    if ($board[$to]===5) {
    	ended($id);
	    $board[1]="BWIN";
    }
    // actually move the piece
    $board[$to]=$board[$from];
    $board[$from]=0;



    //IF YOU CHANGE THIS CHANGE IT FOR CASTLING
    // cycle the turn (only if no one has won yet)
    $board[1] = ($board[1]==="BWIN"||$board[1]==="WWIN")?$board[1]:!$board[1];
    saveBoard($id, $board);
    return json_encode($board);
}

function toIndex($boardx, $boardy) {
    return 2 + $boardy*8 + $boardx;
}
function isPieceBetweenOrthogonal($board, $fromx, $fromy, $tox, $toy) {
    if ($fromy===$toy) {
        //moving horizontal
        $less = min($fromx,$tox);
        $more = max($fromx,$tox);
        for ($i=1+$less;$i<$more;$i++) {
            //if there is a piece there
            if($board[toIndex($i, $fromy)]!=0) return true;
        }
    } else {
        //moving vertical
        $less = min($fromy,$toy);
        $more = max($fromy,$toy);
        for ($i=1+$less;$i<$more;$i++) {
            //if there is a piece there
            if($board[toIndex($fromx, $i)]!=0) return true;
        }
    }
    return false;
}
function isPieceBetweenDiagonal($board, $from, $fromx, $fromy, $to, $tox, $toy) {
    //if moving signs are different
    $inc = ($fromx-$tox===$fromy-$toy)?9:7;
    //moving down-right or up-left
    $less = min($from, $to);
    $more = max($from, $to);
    for ($i=$inc+$less;$i<$more;$i+=$inc) {
        //if there is a piece there
        if($board[$i]!=0) return true;
    }
    return false;
}
