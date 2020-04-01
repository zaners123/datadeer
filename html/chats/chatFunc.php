<?php
function addChat($from, $to, $msg) {
	//data = data.split(/[<>]/).join("").split("\\n").join("<br>");
	$msg = preg_replace("/[<>]/","",$msg);
	$msg = preg_replace("/\n/","<br>&emsp;",$msg);
	$conn = mysqli_connect("localhost","website",parse_ini_file("/var/www/php/pass.ini")["mysql"],"userdata");
	$query = sprintf(
		'insert into pchat (msgtime,msgfrom,msgto,msg) values ("%s","%s","%s","%s")',
		mysqli_real_escape_string($conn, time()),
		mysqli_real_escape_string($conn, $from),
		mysqli_real_escape_string($conn, $to),
		mysqli_real_escape_string($conn, $msg)
	);
	mysqli_query($conn,$query);
	mysqli_close($conn);
}
function readChat($from, $to) {
	$conn = mysqli_connect("localhost","website",parse_ini_file("/var/www/php/pass.ini")["mysql"],"userdata");
	$sql = sprintf(
	//if the message is (to you AND from them) OR (to them AND from you)
		'select msgtime,msgfrom,msgto,msg from pchat where (msgto="%s" and msgfrom="%s") or (msgto="%s" and msgfrom="%s")',
		mysqli_real_escape_string($conn, $to),//to you from them
		mysqli_real_escape_string($conn, $from),
		mysqli_real_escape_string($conn, $from),//to them from you
		mysqli_real_escape_string($conn, $to)
	);

	//set chat to read
	$sqlSetToRead = sprintf(
	//if the message is (to you AND from them)
		'update pchat set msgread = 1 where (msgto="%s" and msgfrom="%s")',
		mysqli_real_escape_string($conn, $from),
		mysqli_real_escape_string($conn, $to)
	);

	//echo $sqlSetToRead;

	//READ - get result from request
	$sqlres = mysqli_query($conn,$sql);

	//WRITE - make it so that the chat is marked as read
	mysqli_query($conn,$sqlSetToRead);

	mysqli_close($conn);

	$rows = array();
	require "/var/www/php/couch.php";
	while ($r = mysqli_fetch_array($sqlres)) {
		$defaultProf = array(
			"username"=>$r["msgfrom"],
			"biography"=>"",
			//a 160x160 PNG, blank by default
			"icon"=>"",
		);

		$userProfile = getDoc("profile",$r["msgfrom"],$defaultProf);

		$row = array();

		if (isset($userProfile["color"])) {
			$row["color"] = $userProfile["color"];
		} else {
			$row["color"] = "#000";
		}
		if (isset($userProfile["icon"])) {
			$row["img"] = $userProfile["icon"];
		} else {
			$row["img"]="";
		}
		$row["name"] = ucfirst($r["msgfrom"]);
		$row["msg"]=$r["msg"];

		//add row/chat to chats sent
		$rows[] = $row;
	}
	return json_encode($rows);
}