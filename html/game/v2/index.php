<?php require "/var/www/php/header.php"; ?>
<title>Games</title>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<!--<link rel="stylesheet" href="/resources/demos/style.css">-->
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
    $(function() {
        $("#tabs").tabs();
    });
</script>

<!--V1 access code (haha not that kind of access code)-->
<script>
//	Public games
    function makeGamePublic(name) {
        fetch("/game/v1/matchmaker.php?gen="+name,{credentials: "same-origin"}).then(function (response) {
            response.text().then(function (gameId) {
                window.location = "/game/v1/gameView.php?id="+gameId;
            });
        });
    }
    function getMatchesPublic() {
        //matchmaker.php, reason = get
        fetch("../v1/matchmaker.php?match=g",{credentials: "same-origin"}).then(function (response) {
            response.text().then(function (text) {
                console.log("REC"+text);
                let res = JSON.parse(text);
                //clear div container
                document.getElementById("table").innerHTML="Public Games:";

                for (let key in res) {
                    if (key==="a" || key[0]==="_") continue;
                    appendRowDataPublic(key,res[key]);
                }
            });
        });
    }
    function applyJSONPublic(res) {


    }
    function appendRowDataPublic(name,link) {
        let gameTypeLong;
        switch (link[0]) {
            case "c":gameTypeLong = "chess";break;
            case "x":gameTypeLong = "checkers";break;
            case "f":gameTypeLong = "connect";break;
            case "b":gameTypeLong = "battleship";break;
        }
        document.getElementById("table").innerHTML+="<div><a href=\"/game/v1/gameView.php?id="+link+"\" style=\"display: inline-block\">"+gameTypeLong+" with "+name+"</a></div>";
    }
	getMatchesPublic();
//  Private Games
    function joinGamePrivate() {
        let gameId = document.getElementById("gameIdInput").value;
        gameId = gameId.split(/[\W]/).join("");
        window.location.replace("/game/v1/gameView.php?id="+gameId);

        return false;
    }
    function makeGamePrivate(name) {
        document.getElementById("table").innerHTML= "Generating "+name+" game...<br>Please wait...";
        fetch("/game/v1/"+name+"Base.php?gencode=true",{credentials: "same-origin"}).then(function (response) {
            response.text().then(function (gameId) {
                document.getElementById("table").innerHTML=
                    name+" game made!<br>Give the opponent the game id <br>\""+gameId+"\"<br>"+" and then " +
                    "<a href=\"/game/v1/gameView.php?id="+gameId+"\">Go Here to Play</a>";
            });
        });
    }
</script>
<?php require "/var/www/php/bodyTop.php"; ?>
<?php require "minesweeper/lib.php"; ?>
<h1>DataDeer Board Games</h1>
<div>
	Join an existing Game:
	<form action="join.php">
		<label>Share ID:<input name="id"></label>
		<input type="submit" value="Join">
	</form>
</div>
<hr>
<div>Start a new game:</div>
<div id="tabs">
	<ul>
		<li><a href="#tabs-0">Chess<br><img width="100vmin" height="100vmin" src="img/chess.png"></a></li>
		<li><a href="#tabs-1">Checkers<br><img width="100vmin" height="100vmin" src="img/checkers.png"></a></li>
		<li><a href="#tabs-2">Battleship<br><img width="200vmin" height="100vmin" src="img/battleship.png"></a></li>
		<li><a href="#tabs-3">Minesweeper<br><img width="100vmin" height="100vmin" src="img/minesweeper.png"></a></li>
		<li><a href="#tabs-4">Sudoku<br><img width="100vmin" height="100vmin" src="img/sudoku.png"></a></li>
		<li><a href="#tabs-5">Tic Tac Toe<br><img width="100vmin" height="100vmin" src="img/tictactoe.png"></a></li>
		<li><a href="#tabs-6">Connect 4<br><img width="100vmin" height="100vmin" src="img/connect4.png"></a></li>
		<li><a href="#tabs-7">Poker</a></li>
	</ul>
	<div id="tabs-0">
		<button onclick="makeGamePrivate('chess')">&#9812; Start a Private Chess Game &#9812;</button><br>
		<button onclick="makeGamePublic('chess')">&#9812; Public Chess Game &#9812;</button><br>
		<a href="/learn/games/chess.php">Learn how to play Chess</a><br>
	</div>
	<div id="tabs-1">
		<button onclick="makeGamePrivate('checkers')">&#9920; Start a Private Checkers Game &#9920;</button><br>
		<button onclick="makeGamePublic('checkers')">&#9920; Public Checkers Game &#9920;</button><br>
	</div>
	<div id="tabs-2">
		<button onclick="makeGamePublic('battleship')">&#128165; Public Battleship Game&#128165;</button><br>
		<button onclick="makeGamePrivate('battleship')">&#128165; Start a Battleship Game &#128165;</button>
	</div>
	<div id="tabs-3">
		<div class="leftHalf">
			<h2>Choose Game Board</h2>
			<h2 id="err" class="red"> </h2>
			<?php foreach ($boardSizes as $size) { echo "<a href='minesweeper/play.php?size=$size[0]'><button>$size[0] mines</button></a></br>";}?>
			<form id="form" action="minesweeper/play.php" onsubmit="return verifyCustom(this);" method="get">
				<input type="button" value="custom" onclick="showCustom()"><br>
				<div id="customSettings" style="visibility: hidden">
					<label>Width: <input type="number" min="1" max="100" value="8" name="width"></label><br>
					<label>Height: <input type="number" min="1" max="100" value="8" name="height"></label><br>
					<label>Mines: <input type="number" min="1" max="9999" value="10" name="mines"></label><br>
					<input type="submit" name="boardSize" value="Play (Custom)">
				</div>
			</form>

			<h2>Continue Game</h2>
			<ul><?php
				$conn = mysqli_connect("localhost","website",parse_ini_file("/var/www/php/pass.ini")["mysql"],"userdata");

				$query = sprintf("select id,round((UNIX_TIMESTAMP(NOW())-time_start)/60,2) as ago from game join game_turn using (id) where time_end=0 and user='%s' order by id desc limit 5;",
					mysqli_escape_string($conn,$_SESSION["username"])
				);

				$res = mysqli_query($conn, $query);
				while ($row = mysqli_fetch_assoc($res)) {
					echo "<li><a class='black' href='minesweeper/play.php?id=".$row["id"]."'>Game #".$row["id"]." - Started ".$row["ago"]." minutes ago</a> </li>";
				}
				?></ul>
		</div>
		<div class="rightHalf">
			<h2>Leaderboard</h2>
			<?php foreach ($boardSizes as $size) {
				echo "<h3>$size[1]: $size[0] mines</h3>";
				$query = sprintf("select user,time_end-time_start as sec from game join game_turn using (id) where time_start < time_end and size='%s' order by sec limit 6;",
					$size[0]
				);
				$res = mysqli_query($conn, $query);
				echo "<ol>";
				while ($row = mysqli_fetch_assoc($res)) {
					echo "<li>".$row["user"]." - ".round($row["sec"],4)."</li>";
				}
				echo "</ol>";
			}?>
		</div>
		<script>
            var errNode = document.getElementById("err");
            var customSettings = document.getElementById("customSettings");
            function err(str) {
                errNode.innerHTML = str;
            }
            function showCustom() {
                customSettings.style.visibility="visible";
            }
            function verifyCustom(formNode) {
                let form = new FormData(formNode);
                if (form.has("board")) {
                    //not custom
                    return true;
                }
                let w = form.get("width");
                let h = form.get("height");
                let m = form.get("mines");
                if (m >= w * h) {
                    err("Mines can't fit");
                    return false;
                }
                return true;
            }
		</script>
	</div>
	<div id="tabs-4">
		<h1><a href="sudoku/play.php?size=3">Play Sudoku Here!</a></h1>
		<div>This page will soon have difficulty settings, leaderboards, etc.</div>
	</div>
	<div id="tabs-5">
		<h1><a href="tictactoe/play.php">Start a game of Tic Tac Toe</a></h1>
	</div>
	<div id="tabs-6">
		<button onclick="makeGamePublic('connect')">&#9922; Public Connect Game &#9922;</button><br>
		<button onclick="makeGamePrivate('connect')">&#9922; Start a Connect Game &#9922;</button><br>
	</div>
	<div id="tabs-7">
		<h1><a href="poker/play.php">Play "Texas hold 'em" Poker (You need 2 or more players, no max player count)</a></h1>
	</div>
</div>
<div id="table">

</div>