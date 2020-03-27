<?php require "/var/www/php/header.php"; ?>
<title>Minesweeper</title>
<style>
	.cell {
		width:64px;
		height:64px;
		font-size: 48px;
		color: #000;
	}
	.boardBase {background: #fff;}
	.boardCurMain {background: #48f9ff;}
	.boardCurFaint {background: #9bf6ff;}
	.boardErr {background: #f99;}
</style>
<?php
require "/var/www/php/bodyTop.php";
require "lib.php";
$board = new SudokuBoard();
if (isset($_GET["id"])) {
	$id = filter_input(INPUT_GET,"id",FILTER_VALIDATE_INT);
	$boardID = $id;
} else if (isset($_GET["size"])) {
	$size = $_GET["size"];
	$board->populateByGenerate($size);
	$boardID = $board->getID();
} else {
	exit("Unknown Input");
} ?>
<h1 class="center">Sudoku (Game #<?=$boardID?>)</h1>
<div id="stats"></div>
<div id="boardHolder">
	<table id="boardViewer">
		<tbody>
		<?php for ($y=0;$y<$board->parseSize()["height"];$y++) {
			echo "<tr>";
			for ($x=0;$x<$board->parseSize()["width"];$x++) {
//				echo "<td class='cell' onmousedown='moused($x,$y)' >?</td>";
//				echo "<td class='cell'>?</td>";
				echo "<td class='cell' oncontextmenu='cursor($x,$y,true);return false;' onclick='cursor($x,$y,false)'>?</td>";
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
    let width = <?=$board->parseSize()["width"]?>;
    let height = <?=$board->parseSize()["height"]?>;
    let mines = <?=$board->parseSize()["mines"]?>;

    const HIDDEN_EMPTY = '<?=MinesweeperBoard::HIDDEN_EMPTY?>';
    const MINE =  '<?=MinesweeperBoard::MINE ?>';
    const HIDDEN_FLAG =   '<?=MinesweeperBoard::HIDDEN_FLAG?>';

    let board = "";
    for(let i=0;i<width*height;i++) board+='?';
    //addEvents();

    function at(x,y) {
        return x*width+y;
    }

    let curX = 0;
    let curY = 0;
    //sets the cursor to be used to place hints
    let curFaint = false;
    /**
     * Moves the cursor
     * */
    function cursor(x,y,faint) {
        curX = x;
        curY = y;
        curFaint = faint;
        setBoardCellClass()
    }

    function toggleFlag(x, y) {
        // console.log("toggle"+x+","+y);
        let i = at(x,y);
        let from = board.charAt(i);
        if (from !== HIDDEN_FLAG && from !== HIDDEN_EMPTY) return;
        setBoardCell(i,board.charAt(i)===HIDDEN_FLAG?HIDDEN_EMPTY:HIDDEN_FLAG);
    }

    function respondToSendBoard(newBoard) {
        //normal routine of updating board
        for (let i = 0; i < width * height; i++) {
            if (newBoard.charAt(i) === board.charAt(i)) continue;
            //don't remove their flags
            if (newBoard.charAt(i) === HIDDEN_EMPTY && board.charAt(i) === HIDDEN_FLAG) continue;
            setBoardCell(i, newBoard.charAt(i));
        }
    }

    function sendBoard() {
        let data = {
            "board": board,
            "id": id,
        };
        let url = "../request.php";
        fetch(url, {
            method: 'POST',
            cache: 'no-cache',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        }).then(function (response) {
            response.text().then(function (newBoard) {
                console.log("Got back \"" + newBoard + "\"");
                if (newBoard === "DEAD") {
                    boardHolder.innerHTML = "BOOM you died<br><a href='..'>Go Back</a>";
                } else if (newBoard.startsWith("DONE")) {
                    boardHolder.innerHTML = "Won!<br>Time: " + newBoard.substr(4) + " seconds<br><br><a href='..'>Go Back</a>";
                    stats.innerHTML = "";
                } else {
                    respondToSendBoard(newBoard);
                }
            });
        });
    }

    function getBoardNode(i) {
        return boardViewer.children[0].children[i%width].children[Math.floor(i/width)];
    }

    function setBoardCellClass(i,c) {
        let n = getBoardNode(i);
        n.className = '';
        n.classList.add("cell");
        n.classList.add(c);
    }

    function setBoardCell(i, val) {
        let from = board.charAt(i);
        board = board.substr(0,i)+val+board.substr(i+1);
        let n = getBoardNode(i);
        n.innerHTML = val;
    }
</script>