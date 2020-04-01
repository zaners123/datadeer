<?php

require "../php/httpHeader.php";

session_start();

//remember user
if (!isset($_SESSION["username"])) {
	require "../php/isRemembered.php";
}

//main LOAD THE WELCOME PAGE
if (isset($_SESSION["username"])) {
	require "../php/header.php"; ?>
	<title id="title">Welcome Back, <?php echo $_SESSION["username"]?></title>
	<style>
		tr{
			margin: 10px;
		}
		th {
			font-weight: bold;
			border-bottom: 2px solid #000;
		}
		td {
			font-weight: bold;
			padding: 5px 10px 5px 10px;
		}
		.gold {
			color: #e6e63c;
			border: 10px solid black;
			background-color: #036c99;
			font-size: 64px;
		}
	</style>
<?php require "/var/www/php/bodyTop.php"; ?>
<!--main front page for signed in peeps-->
<img src="/datadeernet.png" width="50%" height="20%">
<div><a class="black" href="https://boinc.bakerlab.org/">Donate your computing power to fight Coronavirus!</a></div>
<div><a class="black" href="https://github.com/zaners123/datadeer">Check out this site's source code!</a></div>
<div><a class="black" href="/game/v2">WAY more Board Games! Sudoku, TicTacToe, and more!</a></div>
<div><a class="black" href="/deercoin">Now with a currency, DeerCoin! Gamble away your DeerCoin life savings!</a></div>

<?php require "../php/block/directoryLong.php";?>
<?php require "../php/footer.php"; ?>
<?php

} else {

//LOAD THE SIGN IN PAGE
require "../php/headerNoSignin.php"; ?>
<title>Data Deer</title>
</head>
<body style="text-align: left">
<img alt="DataDeer.net" class="deer" src="/datadeernet.png">
<div class="hits">
	<h4 style="line-height: 125%">
		More than 200 users!<br>

		<?php
		require_once "/var/www/php/couch.php";
		$doc = json_decode(getDocUnsafe("hits","souls"),true);
		$srcIP = $_SERVER["REMOTE_ADDR"];
		$doc["ip"][$srcIP] = "1";

		setDoc("hits","souls",$doc);
		echo "Souls: ".sizeof($doc["ip"])."<br>";


		?>
	</h4>
</div>
<div class="home">
<!-- Normal form signin-->
	<div style="font-size: 150%; text-align: center;"><a href="https://boinc.bakerlab.org/" class="black notify">Donate your computing power to fight Coronavirus!</a><br><br></div>
	<form id="signinForm" name="signin" action="/signin.php" method="POST">
		<h3 style="line-height: 125%">
			Username:<input class="homein" placeholder="Username" name="username" id="username" type="text" title="username" required="required"><br>
			&nbsp;Password:<input class="homein" placeholder="Password" name="password" id="password" type="password" title="password" required="required"><br>
			<?php
			//if using the app, default "Remember Me" to true
			if (isset($_GET["rem"])) {?>
			<input type="hidden" name="remember" value="on">
			<?php } else {?>
			<label for="remember">Remember Me:</label><input name="remember" id="remember" class="homecheck homein" type="checkbox">
			<?php }?>
			<input id="submit" class="homein" type="submit" value="Sign In">
		</h3>
	</form>
	<!-- Guest form signin (so required fields stay)-->
	<!--<form style="display: inline" action="/signin.php" method="POST">
		<h3 style="display: inline"><input class="homein" type="submit" name="guest" value="Sign In as Guest"></h3>
	</form>-->
	<!-- Make an account-->
	<form style="display: inline" action="/signup.php" method="POST">
		<h3 style="display: inline"><input class="homein" type="submit" value="Make an Account"></h3>
	</form><br>
	<h4>Say hello and sign the <a href="/guestbook">Guestbook</a>!</h4>
	<!--<h4>Now with an endless <a href="/livestream/">livestream</a> of dog videos (enable autoplay).</h4>-->
	<script>document.getElementById("submit").focus();</script>
	<a href="other/termsofservice.html">Terms of Service</a>
</div>
<?php require "/var/www/php/footer.php"; ?>
<?php } ?>