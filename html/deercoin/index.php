<?php require "/var/www/php/header.php"; ?>
	<title>Deercoin</title>
<?php require "/var/www/php/bodyTop.php"; ?>
<?php
require "/var/www/php/deercoinLib.php";
$conn = mysqli_connect("localhost","website",parse_ini_file("/var/www/php/pass.ini")["mysql"],"userdata");
?>

<h1>Hello! Welcome to the DeerCoin bank!</h1>

<h2>What's DeerCoin?</h2>
It's a DataDeer.net currency!

<h2>How do I get DeerCoin?</h2>
You can get DeerCoin in many ways:
<ul>
	<li>Accounts that already existed start with 100 coins (not new accounts)</li>
	<li>By submitting <a href="/dognet">dog photos</a></li>
	<li>By betting in <a href="/game/v2/#tabs-7">Poker</a></li>
	<li>By going to <a href="casino.php">The Casino</a></li>
</ul>

<h1 id="have"> </h1>

<h2><a href="casino.php">Go to The Casino</a></h2>

<div class="leftHalf">
	<h2>Send DeerCoin</h2>
	<h3 class="red" id="err">
		<?php
		$touser = filter_input(INPUT_POST,"touser",FILTER_VALIDATE_REGEXP,array("options"=>array("regexp"=>"/^\w{4,30}$/")));
		$coins = filter_input(INPUT_POST,"coins",FILTER_VALIDATE_INT);
		if ($touser && $coins) {
			$res = transferCoins($conn, $_SESSION["username"], $touser, $coins,"Direct Transfer");
			if ($res) {
				echo "Transferred ".$coins." DeerCoin to \"".$touser."\"";
			} else {
				echo "Failed transferring ".$coins." DeerCoin to \"".$touser."\". Keep in mind:<br>You can't transfer more than you have<br>You can't transfer to yourself<br>Make sure you typed their name perfectly";
			}
		}
		?>
	</h3>
	<form method="post">
		<label>
			Send To:
			<input type="text" name="touser">
		</label><br>
		<label>
			DeerCoin:
			<input type="number" name="coins" value="1" min="1">
		</label><br>
		<input type="submit" value="Send!">
	</form>
</div>

<div class="rightHalf">
	<h2>Leaderboard</h2>
<ol>
	<?php
	$board = getLeaderboard($conn);
	while ($v = mysqli_fetch_assoc($board)) {
		if ($v["u"]=="Dealer" || $v["u"]=="deer" || $v["u"]=="username" || $v["u"]=="bank") continue;
		echo "<li>".$v["u"]." has ".$v["coins"]." DeerCoin</li>";
	}
	?>
</ol>
</div>

<script>
	document.getElementById("have").innerText = "You have <?=getCoinsOfUser($conn, $_SESSION["username"])?>	DeerCoin";
</script>