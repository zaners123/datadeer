<?php
//main This sends the phone all of the chats the user has yet to read (SQL msgread == 0)
// it is alot more effective than the previous method of sending everything that the app doesn't have (lead to you getting sent things from before you even installed the app)


require "phoneStatus.php";


//what this file does is the phone requests message number 15, so it returns that as a JSON
//you don't need to give it a JSON header because of phoneStatus.php
//header("Content-Type: application/json; charset=UTF-8");

$conn = mysqli_connect("localhost","website",parse_ini_file("/var/www/php/pass.ini")["mysql"],"userdata");
$sql = sprintf(
//if the message is (to you AND not read).
	'select msgnum,msgtime,msg,msgfrom from pchat where (msgread=0 and msgto="%s")',
	mysqli_real_escape_string($conn, $_SESSION["username"])
);
$sqlres = mysqli_query($conn,$sql);
mysqli_close($conn);
$res = array();
while ($r = mysqli_fetch_assoc($sqlres)) {
	$res[] = $r;
}
echo json_encode($res);

//echo '{"time":5,"msg":"Good job signing in. This is MSG'.$_GET["chatNum"].' Hey so how is the family?"}';