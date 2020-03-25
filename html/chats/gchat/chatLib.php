<?php

function addChat($room,$username,$chat) {
	$conn = mysqli_connect("localhost","website",parse_ini_file("/var/www/php/pass.ini")["mysql"]);

//                        data = data.split(/[<>]/).join("").split("\\n").join("<br>");
	$chat = preg_replace("/[<>]/","",$chat);
	$chat = preg_replace("/\n/","<br>&emsp;",$chat);

	mysqli_select_db($conn,"userdata");

	if ($room == "anonymous") $username = "anon";

	$query = sprintf(
		'insert into chat (username,room,text) values ("%s","%s","%s")',
		mysqli_real_escape_string($conn, $username),
		mysqli_real_escape_string($conn, $room),
		mysqli_real_escape_string($conn, $chat)
	);
	mysqli_query($conn,$query);
	mysqli_close($conn);

}
/**Takes the text of that chatroom and reads it*/
function readChat($room) {
	if ($room == "suggest") return "PRIVATE";
	$conn = mysqli_connect("localhost","website",parse_ini_file("/var/www/php/pass.ini")["mysql"],"userdata");
	$sql = sprintf(
		'select username,text from chat where room="%s"',
		mysqli_real_escape_string($conn, $room)
	);
	$sqlres = mysqli_query($conn,$sql);

	$rows = array();
	require_once "/var/www/php/couch.php";
	while ($r = mysqli_fetch_array($sqlres)) {
		//echo implode(",",$r);


		//image icon
		$prof = array(
			"username"=>$r["username"],
			"biography"=>"",
			//a 160x160 PNG, blank by default
			"icon"=>"",
		);
		$userProfile = getDoc("profile",strtolower($r["username"]),$prof);

		//row is the message in chat, with text, color, image, etc
		$row = array();


		if (isset($userProfile["icon"])) {
			$row["img"] = $userProfile["icon"];
		}


		if (isset($userProfile["color"])) {
			$row["color"] = $userProfile["color"];
		} else {
			$row["color"] = "#000";
		}
		$row["name"] = ucfirst($r["username"]);
		$row["msg"]=$r["text"];

		//add row/chat to chats sent
		$rows[] = $row;
	}
	mysqli_close($conn);
	return json_encode($rows);
}