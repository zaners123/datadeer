<?php require "/var/www/php/header.php";?>
<title>Poker</title>
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
	.card {
		width: 20%;
	}
	.uline{border-top:      5px solid black}
	.rline{border-right:    5px solid black}
	.bline{border-bottom:   5px solid black}
	.lline{border-left:     5px solid black}
	.box {border: 1px solid black; background: rgba(206,173,255,0.8)}
</style>
<?php require "/var/www/php/bodyTop.php"; ?>
<?php
require_once "lib.php";
$board = new PokerBoard();
if (isset($_GET["id"])) {
	$board->populateFromID($_GET["id"]);
} else {
	$board->populateByGenerate("");
}
$boardID = $board->getID();
?>
<h1>Poker - Texas Hold 'em - (Share ID: <a href="https://datadeer.net/game/v2/join.php?id=<?=$boardID?>"><?=$boardID?></a>)</h1>
<div id="boardViewer"> </div>
<h2 id="status"> </h2>
<div class="leftHalf">
	<!--	User Input-->
	<div class="box">
		<button style="color: #500" onclick="sendForm(<?=PokerBoard::ACTION_LEAVING;?>)">Leave Game (Folds if applicable)</button>
		<button onclick="sendForm(<?=PokerBoard::ACTION_FOLDING;?>)">Fold</button>
	</div>
	<div class="box">
		<div id="callStat"> </div>
		<button id="callButton" onclick="sendForm(<?=PokerBoard::ACTION_CALLING;?>)">Call</button>
	</div>
	<div class="box">
		<label for="betRange">Next Bet:
			<input id="bet" name="bet" type="number" value="0"/>
			<input id="betRange" type="range" min="0" max="0" value="0" oninput="updateSliderBet(this.value)"/>
		</label>
		<button id="raiseButton" onclick="sendForm(<?=PokerBoard::ACTION_RAISING;?>)">Raise</button>
	</div>
	Your Hand:
	<div id="hand">	</div>
</div>
<div class="rightHalf center">
	<h2 id="pot"> </h2>
	<div style="border: 1px solid black" id="players"> </div>
	<div id="turn"> </div>
	Shown Cards:
	<div id="shown"> </div>
</div>
<script>
	let you="<?=$_SESSION["username"]?>";
	let moneySlider = document.getElementById("betRange");
	let playersE = document.getElementById("players");
	let betE = document.getElementById("bet");
	let timer = document.getElementById("timer");
	let handE = document.getElementById("hand");
	let turnE = document.getElementById("turn");
	let potE = document.getElementById("pot");
	let raiseButtonE = document.getElementById("raiseButton");
	let callStatE = document.getElementById("callStat");
	let shownE = document.getElementById("shown");
	let callButtonE = document.getElementById("callButton");
	let boardViewerE = document.getElementById("boardViewer");
	let lastCall = null;
	//updates slider's text next to it
	function updateSliderBet(val) {
	    betE.value = val;
	}
	function getCardImgHtml(val) {
	    return "<img class='card' alt='"+val+"' src='../img/cards/"+val+".svg'>";
	}
	function player(val) {
	    if (val===you) return "You";
	    return val;
	}
	let board;
    function respondToSendData(resp) {
        if (leave) window.location = "https://datadeer.net/";
	    //testing
        // console.log("Got back:"+JSON.stringify(resp));
        if (resp.startsWith("KICKED")) {
            boardViewerE.innerHTML = "Kicked (commonly done from inactivity or not having enough money to bet)";
            clearInterval(interval);
            return;
        }
        //testing print board
        // document.getElementById("boardViewer").innerHTML = resp;
		board = JSON.parse(resp);
        //tell user the call
        if (lastCall===null) lastCall = board["call"];
        callStatE.innerHTML = "Call of " + board["call"] + " DeerCoin made by " + player(board["callFrom"]);
        //print their hand
        handE.innerHTML = getCardImgHtml(board["hand"][0]) + getCardImgHtml(board["hand"][1]);
        //print players in game
        let playersText = "";
        for (const player of Object.keys(board.players)) {
            // console.log(player);
            let coins = board.players[player];
            if (player===you) {
                playersText += "You have " + coins + " DeerCoins";
            } else {
                playersText += player+" has " + coins+ " DeerCoins";
            }
            if (board["turn"] === player) {
                let time =  Math.round(<?=PokerBoard::SECONDS_PER_TURN?> - (new Date().getTime()/1000 - board["activeSince"]),4);
                if (time < 15) time="<span class='red'>"+time+"</span>";
                if (board["turn"]===you) {
                    playersText+= "<b><- Your Turn, ";
                } else {
                    playersText+= "<b><- "+player+"'s turn, ";
                }
                playersText+= time+" seconds left</b>";
            }
            playersText+= "<br>";
        }
        playersE.innerHTML = "Players:<br>"+playersText;
	    //calculate the part of the pot that is applicable to this user
        let mypot = 0;
        for(let p of board["pot"]) {
            if (p["players"].includes(you)) mypot+=p["amount"];
        }
        potE.innerHTML = "Pot: "+mypot+" DeerCoin";
        //set call button
	    let callText = board["call"]>0?"Call (match "+board["call"]+")":"Check";
	    callButtonE.innerHTML=callText;
        //print shown cards
	    let shown = "";
	    for(let c of board["shown"]) shown+=getCardImgHtml(c);
        shownE.innerHTML = shown;
        //set max bet to your current balance
	    let minRaise = board["call"]+<?=PokerBoard::SMALL_BLUFF_SIZE;?>;
		moneySlider.setAttribute("min",minRaise);
		if (betE.value < minRaise) betE.value = minRaise;
		if (moneySlider.value < minRaise) moneySlider.value = minRaise;
		moneySlider.setAttribute("max",board["players"][you]);
		if (lastCall !== board["call"]) {
            moneySlider.setAttribute("value",board["call"]);
            betE.value = board["call"];
        }
		// updateSliderBet(board["call"]);
        lastCall = board["call"];
    }
    let id = <?=$boardID?>;
    let boardId = <?=$boardID?>;
    function update() {
        sendData(<?=GameBoard::POKER;?>, {"action":<?=PokerBoard::ACTION_UPDATE;?>},respondToSendData);
    }
    let inputURL = "../request.php?gametype=<?=GameBoard::POKER;?>&id=<?=$boardID;?>";
    let leave = false;
    function sendForm(action) {
        let data = {"action":action};
        //if raising, send what they're raising by
        if (action===<?=PokerBoard::ACTION_RAISING;?>) data["bet"] = betE.value;
        sendData(<?=GameBoard::POKER;?>, data, respondToSendData);
        if (action===<?=PokerBoard::ACTION_LEAVING;?>) leave = true;
    }
    //todo make 1000
    let interval = window.setInterval(update,500);
    update();
</script>
