<?php require "/var/www/php/header.php"; ?>
	<title>Deercoin</title>
<?php require "/var/www/php/bodyTop.php"; ?>
<?php
require "/var/www/php/deercoinLib.php";
$conn = mysqli_connect("localhost","website",parse_ini_file("/var/www/php/pass.ini")["mysql"],"userdata");
?>
<h1>Casino</h1>
<hr>
<h2>Flip a coin, if you win you get 10 DeerCoin, if you lose, you lose 12 DeerCoin</h2>
<form method="post"><input type="submit" name="flip" value="Flip"></form>
<?php
error_log(getCoinsOfUser($conn, $_SESSION["username"]));
if (isset($_POST["flip"]) && $_POST["flip"]=="Flip" && getCoinsOfUser($conn, $_SESSION["username"]) > 12) {
	if (rand(0,1)==1) {
		transferCoins($conn, "dealer", $_SESSION["username"], 10,"Casino won coinflip");
		echo "WON 10 coins";
	} else {
		transferCoins($conn, $_SESSION["username"], "dealer", 12,"Casino lost coinflip");
		echo "LOST 12 coins";
	}
	echo "<br>You now have ".getCoinsOfUser($conn, $_SESSION["username"])." Coins";
}?>