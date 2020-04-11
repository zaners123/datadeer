<?php require "/var/www/php/header.php"; ?>
<title>Minesweeper</title>
<script src="../lib.js"></script>
<style>
	.curNormal {background: #48f9ff;}
	.curFaint {background: #fff794;}
	.cell {
		background-clip: padding-box;
		position: relative;
		text-align: center;
		width:64px;
		height:64px;
		font-size: 48px;
		font-weight: bold;
	}
	#boardViewer {
		border-collapse: collapse;
		background: #fff;
	}
	.boardBase {background: #fff;}
	.boardErr {background: #f99;}
	.bold{
		font-weight: bold;
		color: #000;
	}
	.userfilled, .hint{
		color: #0080ff;
		/*background: #ccc;*/
	}
	.uline{border-top: 5px solid black}
	.rline{border-right: 5px solid black}
	.rlines{border-right: 2px solid black}
	.bline{border-bottom: 5px solid black}
	.blines{border-bottom: 2px solid black}
	.lline{border-left: 5px solid black}

	.hint{
		position: absolute;
		/*float:none;*/
		font-size: 16px;
		z-index: 10;
	}
	.hint1{top:0;right:0;}
	.hint2{top:35%;right:0;}
	.hint3{right:0;bottom: 0;}
	.hint4{right:25%;bottom: 0;}
	.hint5{left:25%;bottom: 0;}
	.hint6{bottom:0;left:0;}
	.hint7{top:35%;left:0;}
	.hint8{top:0;left:0;}
	.hint9{top:0;left:40%;}
	/*.num1 { color:#008;}
	.num2 { color:#930;}
	.num3 { color:#f08;}
	.num4 { color:#083;}
	.num5 { color:#80f;}
	.num6 { color:#2af;}
	.num7 { color:#368;}
	.num8 { color:#c83;}
	.num9 { color:#533;}*/
</style>
<?php
require "/var/www/php/bodyTop.php";
require "lib.php";
$board = new SudokuBoard();
if (isset($_GET["id"])) {
	$id = filter_input(INPUT_GET,"id",FILTER_VALIDATE_INT);
	$board->populateFromID($id);
} else if (isset($_GET["size"])) {
	$size = $_GET["size"];
	$board->populateByGenerate($size);
} else {
	exit("Unknown Input");
}
$boardID = $board->getID();

?>
<h1 class="center">Sudoku (Game #<?=$boardID?>)</h1>
<h2 id="stats">
	Recommended to play using WASD and your numpad.
	WASD,arrows, or clicking moves the cursor,
	typing zero or h toggles the cursor into "hint mode",
	typing a number sets the value at the cursor,
	pressing space, delete, or backspace clears a cell.
</h2>
<div class="center" id="boardHolder">
	<table align="center" id="boardViewer">
		<tbody>
		<?php for ($y=0;$y<$board->parseSize()["height"];$y++) {
			echo "<tr>";
			for ($x=0;$x<$board->parseSize()["width"];$x++) {
				echo "<td class='cell ".($x==0?"lline":"")." ".($y==0?"uline":"")." ".($y%3==2?"bline":"blines")." ".($x%3==2?"rline":"rlines")."' oncontextmenu='cursor($x,$y,true);return false;' onclick='cursor($x,$y,false)'>?</td>";
			}
			echo "</tr>";
		}?>
		</tbody>
	</table>
</div>
<script>
    function setLocalBoard(clientData,hiddenData) {
        if (clientData.length !== width * height) alert("ERR bad size 1");
        if (hiddenData.length !== width * height) alert("ERR bad size 2");
        for (let i = 0; i < width * height; i++) {
            if (hiddenData[i]!==' ') {
                setBoardCell(i,hiddenData[i]);
                setBoardCellClass(i,"bold");
            } else {
                setBoardCell(i,clientData[i]);
                setBoardCellClass(i,"userfilled");
            }
        }
    }

    let boardHolder = document.getElementById("boardHolder");
    let boardViewer = document.getElementById("boardViewer");

    let boardId = <?=$boardID?>;
    let id = <?=$boardID?>;
    let width = 9;
    let height = 9;

    let board = "";

    let curX = 0;
    let curY = 0;
    //sets the cursor to be used to place hints
    let curFaint = false;
    /**
     * Moves the cursor
     * */
    function cursor(x=curX,y=curY,faint = curFaint) {
        setBoardCellClass(at(curX,curY),"curFaint",false);
        setBoardCellClass(at(curX,curY),"curNormal",false);
        curX = x;
        curY = y;
        curFaint = faint;
        setBoardCellClass(at(curX,curY),curFaint?"curFaint":"curNormal");
    }

    function toggleCursorFaint() {
        curFaint = !curFaint;
        cursor();
    }

    let hints = [];
    for(let i=0;i<81;i++) {
        hints[i] = [];
        for(let b=1;b<=9;b++) hints[i][b]=false;
    }

    function setCursor(number, faint=curFaint) {
        let i= at(curX,curY);
        //dont set the initially given hints
        if (getBoardHasClass(i,"bold")) return;
        //allow clearing cells in hint mode
        if (number===' ' && faint===true) setCursor(' ',false);
        if (faint) {
            //set little hints
	        hints[i][number] = !hints[i][number];
	        let html = " ";
	        for(let hintPos=1;hintPos<=9;hintPos++) {
	            if (hints[i][hintPos]) {
	                html+="<div class='hint num"+hintPos+" hint"+hintPos+"'>"+hintPos+"</div>";
	            }
	        }
	        getBoardNode(i).innerHTML = html;
        } else {
            //remove hints when setting value
            hints[i]=[];
            setBoardCell(i,number);
            sendBoard(<?=GameBoard::SUDOKU?>);
            for(let i=1;i<=9;i++)setBoardCellClass(i,"hint"+number,false);
            setBoardCellClass(i,"num"+number);

            setBoardCellClass(i,"userfilled");
        }
    }

    document.body.onkeydown = function(e) {
        console.log(e);
       let key = e["key"];
       if (key==='0' || key==='h') {toggleCursorFaint();
       } else if (key===' ' || key==="Backspace" || key==="Delete") {setCursor(' ');
       } else if ((key >= '1' && key <= '9')) {setCursor(key);
       } else if (key === "ArrowDown" || key==="s") {if (curY<8)cursor(curX,curY+1);
       } else if (key === "ArrowLeft" || key==="a") {if(curX>0)cursor(curX-1,curY);
       } else if (key === "ArrowRight"|| key==="d") {if(curX<8)cursor(curX+1,curY);
       } else if (key === "ArrowUp"   || key==="w") {if(curY>0)cursor(curX,curY-1);
       }
    };

    function respondToSendData(resp) {
        if (resp["state"]!=="") {
            if (resp["state"]==="DONE") {
                boardHolder.innerHTML = "Won!<br>Time: " + resp["state"].substr(4) + " seconds<br><br><a href='..'>Go Back</a>";
            }
        }
    }

    //main init code
    setLocalBoard("<?=$board->getClientBoard(); ?>","<?=$board->getHiddenBoard(); ?>");
    cursor(0,0,false);
</script>