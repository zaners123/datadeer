<?php require "/var/www/php/header.php"; ?>
	<title>Deercoin</title>
<?php require "/var/www/php/bodyTop.php"; ?>
<?php
require "/var/www/php/deercoinLib.php";
$conn = mysqli_connect("localhost","website",parse_ini_file("/var/www/php/pass.ini")["mysql"],"userdata");
?>
<h1>Casino</h1>
<h2>Don't spam the buttons, click max like once per second or it will say "Error"</h2>
<h1 id="have"> </h1>
<hr>
<h2>Flip a coin, if you win you get 10 DeerCoin, if you lose, you lose 10 DeerCoin</h2>
<form method="post"><input type="submit" name="flip" value="Flip"></form>
<?php
if (
	isset($_POST["flip"]) && $_POST["flip"]=="Flip" &&
	transferCoins($conn, $_SESSION["username"], "dealer", 10,"Casino bet coinflip")) {
	if (rand(0,1)==1) {
		$winstate = transferCoins($conn, "dealer", $_SESSION["username"], 20,"Casino won coinflip");
		if (!$winstate) echo "ERROR COIN LANDED ON SIDE, CONTACT OP";
		echo "<h1>WON 10 coins</h1>";
	} else {
		echo "</h3>LOST 10 coins</h1>";
	}
} else {
	echo "Press 'Flip' to flip";
}?>
<hr>
<h2>Roll a 100-sided die for 10 coins. If it lands on 100, you get 1000 coins</h2>
<form method="post"><input type="submit" name="roll" value="Roll"></form>
<?php
if (
	isset($_POST["roll"]) && $_POST["roll"]=="Roll" &&
	transferCoins($conn, $_SESSION["username"], "dealer", 10,"Casino bet dice")) {
	if (($roll = rand(1,100))==100) {
		$winstate = transferCoins($conn, "dealer", $_SESSION["username"], 1010,"Casino won dice");
		if (!$winstate) echo "ERROR DIE FELL OFF TABLE, CONTACT OP";
		echo "<h1 class='rainbow'>WON 1000 coins</h1>";
		echo "<h1>WON 1000 coins</h1>";
		echo "<h1 class='rainbow'>WON 1000 coins</h1>";
	} else {
		echo "<h3>LOST 10 coins (rolled a ".$roll.")</h3>";
	}
} else {
	echo "Press 'Roll' to roll";
}?>
<hr>
<h2>Flip a giant coin. If it lands heads, your money doubles! If it lands tails, your money goes to zero. May the odds be in your favor...</h2>
<form method="post">
    <input type="submit" name="bigflip" value="BigFlip">
</form>
<?php
$bet = getCoinsOfUser($conn, $_SESSION["username"]);
if (
	isset($_POST["bigflip"]) && $_POST["bigflip"]=="BigFlip" &&
	transferCoins($conn, $_SESSION["username"], "dealer", $bet,"Casino bet coinflip")) {
	if (rand(0,1)==1) {
		$winstate = transferCoins($conn, "dealer", $_SESSION["username"], $bet*2,"Casino won coinflip");
		if (!$winstate) echo "ERROR COIN LANDED ON SIDE, CONTACT OP";
		echo "<h1>WON $bet coins</h1>";
	} else {
		echo "</h3>LOST $bet coins</h1>";
	}
} else {
	echo "Press 'BigFlip' to flip";
}?>
<hr>
<script>
    document.getElementById("have").innerText = "You have <?=getCoinsOfUser($conn, $_SESSION["username"])?>	DeerCoins";
</script>