<?php require "/var/www/php/header.php"; ?>
	<title>Legends only</title>
<?php require "/var/www/php/bodyTop.php"; ?>
If you are truly a Computer Nerd, what's this minimally output?<br>
<pre>
int a = 16*15^2;
long b = 19560-12/23;
char f = sizeof(a) + sizeof(b);
std::cout<<(long int)f;
</pre>

<form method="post">
	<label>
		Answer:
		<input type="number" name="ans" placeholder="Answer">
	</label>
	<input value="Submit" type="submit">
</form>
<?php
require_once "/var/www/php/couch.php";
$doc = getDoc("profile",$_SESSION["username"],$blankDefault);
if (isset($_POST["ans"]) && $_POST["ans"]==="12") {
	$doc["nerd"]="true";
	setDoc("profile",$_SESSION["username"],$doc);
}

if ($doc["nerd"]==="true") {
	echo "YOU ARE A NERD, Continue...";
}
require "/var/www/php/footer.php";