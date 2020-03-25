<?php
/*

A server-side minesweeper (so leaderboards are valid)
(Couldn't be client-side because they could cheat to see the entire board)

Game State:
    Generates valid board, sends client stripped version (where mines are unknown)
        Client chooses coordinates (through AJAX CALL), server responds with new game state (new board info)
            Flags are client-side
            If mine hit, client dies

*/
?>
<?php require "/var/www/php/header.php"; ?>
<title>Minesweeper</title>
<style>
	/*#boardViewer {
		width:100%;
		height: 100%;
	}*/
	.cell {
		width:64px;
		height:64px;
		font-size: 48px;
	}
	.board0 {background: #fff;color: #fff;}
	.board1 {background: #fff;color: #11f;}
	.board2 {background: #fff;color: #0f0;}
	.board3 {background: #fff;color: #f00;}
	.board4 {background: #fff;color: #009;}
	.board5 {background: #fff;color: #800;}
	.board6 {background: #fff;color: #0ff;}
	.board7 {background: #fff;color: #80f;}
	.board8 {background: #fff;color: #0ff;}
	.boarde {background: #333;color: #333;}
	.boardf {background: #66f;color: #f00;}
</style>
<?php
require "/var/www/php/bodyTop.php";
//set specified board
if (!preg_match("/^(\d+)x(\d+), (\d+) mines$/",$_GET["boardSize"],$boardSize)) {
	if (isset($_GET["width"]) && isset($_GET["height"]) && isset($_GET["mines"])) {
		//set custom board
		$width = filter_var($_GET["width"],FILTER_VALIDATE_INT);
		$height = filter_var($_GET["height"],FILTER_VALIDATE_INT);
		$mines = filter_var($_GET["mines"],FILTER_VALIDATE_INT);
	} else {
		exit("Invalid Input");
	}
} else {
$width = $boardSize[1];
$height = $boardSize[2];
$mines = $boardSize[3];
}

//check dimensions
if ($mines+1 >= $width * $height) exit("Too many mines");
if ($width<1 || $width > 100 || $height<1 || $height > 100) exit("Bad size");

require "lib.php";
$board = new MinesweeperBoard();
$board -> constructByGenerate(array("width"=>$width,"height"=>$height,"mines"=>$mines));
$boardID = $board->getID();
?>
<h1 class="center">Minesweeper (Game #<?=$boardID?>)</h1>
<div id="stats"></div>
<div id="boardHolder">
	<table id="boardViewer">
		<tbody>
		<?php for ($y=0;$y<$height;$y++) {
			echo "<tr>";
			for ($x=0;$x<$width;$x++) {
//				echo "<td class='cell' onmousedown='moused($x,$y)' >?</td>";
//				echo "<td class='cell'>?</td>";
				echo "<td class='cell' oncontextmenu='toggleFlag($x,$y);return false;' onclick='sendLoc($x,$y)'>?</td>";
			}
			echo "</tr>";
		}?>
		</tbody>
	</table>
</div>
<script>
    let boardHolder = document.getElementById("boardHolder");
    let boardViewer = document.getElementById("boardViewer");
    let stats = document.getElementById("stats");

    let boardId = <?=$boardID?>;
    let width = <?=$width?>;
    let height = <?=$height?>;
    let mines = <?=$mines?>;

    let covered = width*height;

    const HIDDEN_EMPTY = '<?=MinesweeperBoard::HIDDEN_EMPTY?>';
    const MINE =  '<?=MinesweeperBoard::MINE ?>';
    const HIDDEN_FLAG =   '<?=MinesweeperBoard::HIDDEN_FLAG?>';

    let board = "";
    for(let i=0;i<width*height;i++) board+='?';
    //addEvents();

    function at(x,y) {
        return x*width+y;
    }

    /*function moused(e,x,y) {
        console.log("event "+x+","+y+","+e.which);
    }

    function addEvents() {
        for (let y=0;y<height;y++) {
            for (let x=0;x<width;x++) {
                boardViewer.children[0].children[y].children[x].onmousedown = function(e) {
                    moused(e,x,y);
                };
            }
        }
    }*/

    function toggleFlag(x, y) {
        // console.log("toggle"+x+","+y);
        let i = at(x,y);
        let from = board.charAt(i);
        if (from !== HIDDEN_FLAG && from !== HIDDEN_EMPTY) return;
        set(i,board.charAt(i)===HIDDEN_FLAG?HIDDEN_EMPTY:HIDDEN_FLAG);
    }

    function updateStats() {
        stats.innerHTML = "Tiles left to click: "+(covered-mines);
    }

    function sendLoc(x, y) {
        //if its a flag, dont
	    if (board.charAt(at(x,y)) === HIDDEN_FLAG) return;


        let url = "request.php?x=" + x + "&y=" + y + "&id=" + boardId;
        console.log("went to \"" + url + "\"");
        fetch(url, {credentials: "same-origin"}).then(function (response) {
            response.text().then(function (newBoard) {
                console.log("Got back \"" + newBoard + "\"");
                if (newBoard === "DEAD") {
                    boardHolder.innerHTML = "BOOM you died<br><a href='.'>Go Back</a>";
                } else if (newBoard.startsWith("DONE")) {
                    boardHolder.innerHTML = "Won!<br>Time: " + newBoard.substr(4) + " seconds<br><br><a href='.'>Go Back</a>";
                    stats.innerHTML = "";
                } else {
                    //normal routine of updating board
                    for (let i = 0; i < width * height; i++) {
                        if (newBoard.charAt(i) === board.charAt(i)) continue;
                        //don't remove their flags
                        if (newBoard.charAt(i) === HIDDEN_EMPTY && board.charAt(i) === HIDDEN_FLAG) continue;
                        set(i, newBoard.charAt(i));
                    }
                    updateStats();
                }
            });
        });
    }

    function set(i,val) {
        let from = board.charAt(i);
        if (val >= '0' && val <= '9') covered--;


        // console.log("setting "+board.charAt(i)+" to "+val);
        board = board.substr(0,i)+val+board.substr(i+1);
        let n = boardViewer.children[0].children[i%width].children[Math.floor(i/width)];
        n.innerHTML = val;
        n.className = '';
        n.classList.add("cell");
        n.classList.add("board"+val);
    }
</script>