<?php require "/var/www/php/header.php" ?>
<title>Private Chat</title>
<meta http-equiv="refresh" content="15">
<?php require "/var/www/php/bodyTop.php" ?>
<div style="text-align: center">
<h1>
	Private Chat (or go to <a href="/chat">Group chat</a>):

	<br><br>

	Owner's username is "deer"
	<br><br>

	Chat with a user (username case sensitive):
	<form onsubmit="return joinChat()">
		<input name="with" id="user" type="text" placeholder="Username"/>
		<input type="submit" value="Join"/>
	</form>

</h1>

<br>
<?php
$conn = mysqli_connect("localhost","website",parse_ini_file("/var/www/php/pass.ini")["mysql"],"userdata");

//echo recent 5 messages

echo "<h1 style='display: inline'>Recent Messages:</h1><br><h2 style='display: inline'><br>";
$sql = sprintf(
	'select * from pchat where (msgto="%s" or msgfrom="%s") order by msgtime desc limit 5',
	mysqli_real_escape_string($conn, $_SESSION["username"]),
	mysqli_real_escape_string($conn, $_SESSION["username"])
);// order by msgtime desc
//echo $sql;
$sqlres = mysqli_query($conn,$sql);
while ($r = mysqli_fetch_array($sqlres)) {
	$msg = substr($r["msg"], 0, 32);
	$msg = preg_replace("/\n/"," ",$msg);
	if ($_SESSION["username"]===$r["msgfrom"]) {
		$name = $r["msgto"];
	} else {
		$name = $r["msgfrom"];
	}
	echo "<a href='chatroom.php?user=".$name."'>".$r["msgfrom"].">".$r["msgto"]."</a>: ".$msg."<br>";
}




//echo your friends sorted by recency

echo "<br><br></h2><h1 style='display: inline'>Recent Friends:</h1><br><h2 style='display: inline'>";
$sql = sprintf(
//if to or from them, sort by time
	'select distinct MAX(msgtime),msgfrom,msgto from pchat where (msgto="%s" or msgfrom="%s") group by msgfrom,msgto order by MAX(msgtime) desc',
	mysqli_real_escape_string($conn, $_SESSION["username"]),
	mysqli_real_escape_string($conn, $_SESSION["username"])
);// order by msgtime desc
//echo $sql;
$sqlres = mysqli_query($conn,$sql);
$listed = array();
while ($r = mysqli_fetch_array($sqlres)) {
	if ($r["msgto"]==="") continue;
	//echo implode(",",$r);
	if ($_SESSION["username"]===$r["msgfrom"]) {
		if (isset($listed[$r["msgto"]])) continue;
		$listed[$r["msgto"]]="1";
		$name = $r["msgto"];
	} else {
		if (isset($listed[$r["msgfrom"]])) continue;
		$name = $r["msgfrom"];
		$listed[$r["msgfrom"]]="1";
	}
	echo "<a href='chatroom.php?user=".$name."'>".$name."</a><br>";
	//$listed[] = ucfirst($r["msgfrom"]).">".$r["msgto"];
}
echo "</h2>";






mysqli_close($conn);
//echo json_encode($rows);
?>

<script>
    function joinChat() {
        window.location="chatroom.php?user="+(document.getElementById("user").value);
        return false;
    }
</script>
</div>
<?php require "/var/www/php/footer.php" ?>
