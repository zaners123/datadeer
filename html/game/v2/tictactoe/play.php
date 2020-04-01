<?php require "/var/www/php/header.php";?>
	<title>Tic Tac Toe</title>
	<script src="../lib.js"></script>
	<style>
		.board {
			font-size: 64px;
			width: 64px;
			height: 64px;
		}
		#boardViewer {
			border-collapse: collapse;
		}
		.uline{border-top:      5px solid black}
		.rline{border-right:    5px solid black}
		.bline{border-bottom:   5px solid black}
		.lline{border-left:     5px solid black}
	</style>
<?php require "/var/www/php/bodyTop.php"; ?>
<?php
require_once "lib.php";
$board = new TicTacToeBoard();
if (isset($_GET["id"])) {
	$board->populateFromID($_GET["id"]);
} else {
	$board->populateByGenerate("");
}
$boardID = $board->getID();
?>
<script>
	let id = <?=$boardID?>;
	function clicked(i) {
	    let url = "../request.php?gametype=5&i="+i+"&id="+id;
        console.log("went to \"" + url + "\"");
        fetch(url, {credentials: "same-origin"}).then(function (response) {
            response.text().then(function (response) {
                console.log("Got back \"" + response + "\"");
                let resp = JSON.parse(response);
                if (parseInt(resp["players"]) !== 2) {
                    setStatus(resp["players"]+"/2 players so far...");
                } else if (resp["state"]!=="") {
                    switch (resp["state"]) {
                        case "DONE":setStatus("You Won!");break;
                        case "DEAD":setStatus("You Lost!");break;
                        case "TIED":setStatus("You Tied!");break;
                        default:setStatus("Unknown Err");break;
                    }
                    window.clearInterval(interval);
                } else {
                    setStatus(resp["active"]+"'s turn, waiting for player's input");
                }
	            for(let i=0;i<9;i++) {
	                let y = Math.floor(i/3);
	                let x = i%3;
	                document.getElementById("board").children[y].children[x].innerHTML=resp["board"][i];
                }
            });
        });
	}
	let interval = window.setInterval(clicked,1000,100);
</script>
<h1>Tic Tac Toe (Share ID: <a href="https://datadeer.net/game/v2/join.php?id=<?=$boardID?>"><?=$boardID?></a>)</h1>
<h2 id="status">Loading the game...</h2>
<div class="center">
	<table id="boardViewer" align="center">
		<tbody id="board">
			<?php for ($y=0;$y<3;$y++) { ?>
			<tr>
				<?php for ($x=0;$x<3;$x++) { ?>
				<td class="board <?=($x!=0?"lline ":"").($y!=0?"uline ":"").($y!=2?"bline ":"").($x!=2?"rline":"")?>" onclick="clicked(<?=$x+$y*3?>)"> </td>
				<?php } ?>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>