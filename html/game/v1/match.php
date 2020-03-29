<?php require "/var/www/php/header.php"; ?>
<title>Match Maker</title>
<?php require "/var/www/php/bodyTop.php"; ?>
<h1 style="text-align: center">Join a Public Game</h1>
<div id="table">
	<div style="font-size: 200%;padding-left: 16px;padding-right: 16px;border: 4px solid black">
		<a href="." style="display: inline-block">Deer - Chess</a>
	</div>
</div>
<div style="text-align: center">
	<button onclick="makeGame('chess')">&#9812; Public Chess Game &#9812;</button><br>
	<button onclick="makeGame('checkers')">&#9920; Public Checkers Game &#9920;</button><br>
	<button onclick="makeGame('connect')">&#9922; Public Connect Game &#9922;</button><br>
	<button onclick="makeGame('battleship')">&#128165; Public Battleship Game&#128165;</button><br>
</div>
<script>
    function makeGame(name) {
        fetch("matchmaker.php?gen="+name,{credentials: "same-origin"}).then(function (response) {
            response.text().then(function (gameId) {
                window.location = "/game/gameView.php?id="+gameId;
            });
        });
    }
    function getMatches() {
        //matchmaker.php, reason = get
        fetch("matchmaker.php?match=g",{credentials: "same-origin"}).then(function (response) {
            response.text().then(function (text) {
                console.log("REC"+text);
                applyJSON(JSON.parse(text));
            });
        });
    }
    function applyJSON(res) {
        console.log("I got a JSON:");
        console.log(res);

        //clear div container
        document.getElementById("table").innerHTML="";

        for (let key in res) {
            if (key==="a" || key[0]==="_") continue;
            appendRowData(key,res[key]);
        }

    }
    function appendRowData(name,link) {
        let gameTypeLong;
        switch (link[0]) {
            case "c":gameTypeLong = "chess";break;
            case "x":gameTypeLong = "checkers";break;
            case "f":gameTypeLong = "connect";break;
            case "b":gameTypeLong = "battleship";break;
        }
        document.getElementById("table").innerHTML+="<div style=\"font-size: 200%;padding-left: 16px;padding-right: 16px;border: 4px solid black\">\n" +
            "\t\t<a href=\"gameView.php?id="+link+"\" style=\"display: inline-block\">"+name+" - "+gameTypeLong+"</a>\n" +
            "\t</div>";

    }
    getMatches();
</script>
<?php require "/var/www/php/footer.php"; ?>