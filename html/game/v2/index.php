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
<?php require "/var/www/php/bodyTop.php"; ?>
<?php require "minesweeper/lib.php"; ?>
<div id="tabs">
	<ul>
		<li><a href="#tabs-0">Chess</a></li>
		<li><a href="#tabs-1">Checkers</a></li>
		<li><a href="#tabs-2">Battleship</a></li>
		<li><a href="#tabs-3">Minesweeper</a></li>
		<li><a href="#tabs-4">Sudoku</a></li>
	</ul>
	<div id="tabs-0">
		<a href="/game">Game</a>
	</div>
	<div id="tabs-1">
		<a href="/game">Game</a>
	</div>
	<div id="tabs-2">
		<a href="/game">Game</a>
	</div>
	<div id="tabs-3">
		<div class="leftHalf">
			<h2>Choose Game Board</h2>
			<h2 id="err" class="red"></h2>
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
		Not done yet
	</div>
</div>