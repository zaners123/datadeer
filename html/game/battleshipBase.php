<?php
$readOnlyDisabled = true;
require "/var/www/php/requireSignIn.php";

require "boardMaster.php";

if (isset($_GET["gencode"]) && $_GET["gencode"] === "true") {
    $newId="b";//all chess game IDs start with a c, checkers should start with an x, battleship b
    $characters = array_merge(range('a','z'), range('0','9'));
    $max = count($characters)-1;
    for ($i = 0; $i < 5; $i++) {
        //if ($i==5 || $i == 10) $str.="-";
        $rand = mt_rand(0, $max);
        $newId .= $characters[$rand];
    }
    $board = array_merge(
        array($_SESSION["username"],true),//the board starts with a user, and the fact that it is their turn. Once they go, the turn bit flips.
        array_fill(0,202,0)//the blank board
        //BOARD ENDS WITH BOTH PLAYER'S BOAT COUNTS
    );
    saveBoard($newId, $board);
    echo $newId;
    return;
} else if (isset($_GET["id"])) {
    if (isset($_GET["move"])) {
        echo makeMove($_GET["id"], $_GET["move"]);
    } else {
    	$board = json_decode(loadBoard($_GET["id"]));
        echo json_encode(boardRemOpp($board, isOwner($board)));
	    //echo loadBoard($_GET["id"]);
    }
}

function isOwner($board) {
    return ($board[0]===$_SESSION["username"]);
}
/**
    Ships:
 *      5, 4, 3, 3, 2 (total: 17)

 */
function makeMove($id, $move) {

    //request move would be "?move=12" to bomb "B2" (YOU CAN USE BASE 10!!! Starts at zero)
    $move = intval(preg_replace("/[\D]/","",$move));
    //if move out of range, say no
    if ($move < 0 || $move > 100) return "no0";


    $board = json_decode(loadBoard($id));
    $isOwner = isOwner($board);

    //move is set to the proper board
    $move += 2;

    //if you need to place ships
    if (shipsForUser($board, $isOwner) < 17 /*    && boatsLineUp($board)   */) {
        $move += $isOwner?0:100;
        //TODO make it so boats have to line up (not just 17 1x1 boats)

        //toggle boat in location
        if ($board[$move]==0) {
            //placing boat
            $board[$move] = 2;
            //increment their placed boat count
            $board[$isOwner?202:203]++;
        } else {
            //removing boat
            $board[$move] = 0;
            //decrement their placed boat count
            $board[$isOwner?202:203]--;
        }
    } else {
        if (shipsForUser($board, !$isOwner) >= 17 /* && boatsLineUp($board)  */) {
            //if both players have 17 boats (GAME RUNNING)

            //check that it's their turn
            if ($isOwner !== $board[1]) return "no0b";

            //set move to opponent's board
            $move += $isOwner?100:0;

            //if they shot at a blank or boat
            if ($board[$move] == 0 || $board[$move] == 2) {
                //mark as shot at
                $board[$move]++;
                //if it is a hit
                if ($board[$move] == 3) {
                    if (++$board[$isOwner?202:203] == 34) {
                        //if you took all of their boats
                        if ($isOwner) {
                            $board[1] = "WWIN";
                        } else {
                            $board[1] = "BWIN";
                        }
	                    ended($id);
                    }
                }

                // cycle the turn after a shot (only if no one has won yet)
                $board[1] = ($board[1]==="BWIN"||$board[1]==="WWIN")?$board[1]:!$board[1];
            } else {
            	return "no0c";
            }
        } else {
            //the opponent is still setting up their ships
            return "no1";
        }
    }

    saveBoard($id, $board);

    //hide the opponent's boats after moving
	$board = boardRemOpp($board,$isOwner);


    return json_encode($board);
}
function boardRemOpp($board, $isOwner) {
	$max=$isOwner?202:102;
	for ($i=$isOwner?102:2 ; $i < $max ; $i++) {
		if ($board[$i] == 2) $board[$i]=0;
	}
	return $board;
}

function shipsForUser($board, $owner) {
    return $board[$owner?202:203];
    //return
    /*$res = 0;

    //iterate through board, start at the start of their board
    $max=$owner?102:202;
    for ($i=$owner?2:102; $i < $max ; ++$i) {
        //if it is their boat, increment res
        if ($board[$i]==2 || $board[$i]==3) $res++;
    }
    return $res;*/
}