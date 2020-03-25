<?php require "/var/www/php/header.php"; ?>
	<title>Games</title>
    <style>
        button {
            font-size: 120%;
        }
    </style>
<?php require "/var/www/php/bodyTop.php"; ?>
	<script>
        function joinGame() {
            let gameId = document.getElementById("gameIdInput").value;
            gameId = gameId.split(/[\W]/).join("");
            window.location.replace("/game/gameView.php?id="+gameId);

	        return false;
        }
        function makeGame(name) {
            document.getElementById("frame").innerHTML= "Generating "+name+" game...<br>Please wait...";
            fetch(name+"Base.php?gencode=true",{credentials: "same-origin"}).then(function (response) {
                response.text().then(function (gameId) {
                    document.getElementById("frame").innerHTML=
                            name+" game made!<br>Give the opponent the game id <br>\""+gameId+"\"<br>"+" and then " +
                            "<a href=\"/game/gameView.php?id="+gameId+"\">Go Here to Play</a>";
                });
            });
        }
    </script>
    <h1 id="frame">
	    <table style="border: 1px solid black">
		    <thead>
			    <tr>
				    <td>PUBLIC</td>
				    <td>PRIVATE</td>
			    </tr>
		    </thead>
		    <tbody>
		        <tr>
			        <td style="padding-right: 32px"><button onclick="window.location = '/game/match.php'">&#x1F465; Public Match &#x1F465;</button><br></td>
			        <td><form onsubmit="return joinGame()"><input id="gameIdInput" type="text" placeholder="Game ID from opponent"/></form></td>
		        </tr>
		    <tr>
			    <td></td>
			    <td>
				    <form onsubmit="return joinGame()"><input type="submit" value="Join Game"></form>
			    </td>
		    </tr>
		    </tbody>
	    </table><br>
	    Start a Private Game<br>
	    <button onclick="makeGame('chess')">&#9812; Start a Chess Game &#9812;</button><br>
	    <a href="/learn/games/chess.php">Learn how to play Chess</a><br>
	    <button onclick="makeGame('checkers')">&#9920; Start a Checkers Game &#9920;</button><br>
        <button onclick="makeGame('connect')">&#9922; Start a Connect Game &#9922;</button><br>
        <button onclick="makeGame('battleship')">&#128165; Start a Battleship Game &#128165;</button>
	    <br>
    </h1>
<?php require "/var/www/php/footer.php"; ?>