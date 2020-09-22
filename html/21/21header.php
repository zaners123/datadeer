<?php
require_once "/var/www/php/requireSignIn.php";
require_once "/var/www/php/couch.php";
$doc = getDoc("profile",$_SESSION["username"],$blankDefault);
if (isset($_POST["adult"]) && strtolower($_POST["adult"])===parse_ini_file("/var/www/php/pass.ini")) {
	$doc["adult"] = isset($_POST["adult"])?"true":"false";
	setDoc("profile",$_SESSION["username"],$doc);
}
require_once "/var/www/php/headerContent.php";
echo '<link rel="stylesheet" type="text/css" href="/css/21.css"/>';
if (isset($doc["adult"])) {
	return;
}
?>
<head>
	<title>Are you Old Enough?</title>
	<style>
		body {
			margin-left: 10%;
			margin-right: 10%;
		}
		*{
			text-align: center;
			margin-top: 32px;

			font-size: 32px;
			background: #300;
			color: #fff;
		}</style>
</head>
<body>
<h1 style="font-size: 48px">Are you Old Enough?</h1>
<form method="post">
	<p>
		This contains nasty stuff. Cuss words. Opinions. Or worse, politics! Don't go in here if you don't want to.
	</p>
	<p>
		Type the code into The Box to continue.
	</p>
	<p>
		<input type="text" name="adult" placeholder="The Box">
	</p>
	<input type="submit" value="Submit">
</form>
<?php
//go no further
exit;
