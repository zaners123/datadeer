<?php
/*

+-------------+-------------+------+-----+---------+-------+
| Field       | Type        | Null | Key | Default | Extra |
+-------------+-------------+------+-----+---------+-------+
| username    | varchar(32) | NO   | PRI | NULL    |       |
| remembercol | mediumtext  | NO   |     | NULL    |       |
+-------------+-------------+------+-----+---------+-------+

 */
//echo $_COOKIE["rememberuser"];
//echo $_COOKIE["rememberkey"];

/**
 *
 * PSEUDO CODE
 * sessionStart needs to be already called, and cookies need to be set
 *
 * if (sql table has user=rememberuser AND id=remember) {
 *      session[username] = rememberuser
 * } else {
 *      not remembered. Do nothing
 * }
 *
 */

//if session is already set, return
if (isset($_SESSION["username"])) {
	return;
}

//if not both cookies are set, return
if (!(isset($_COOKIE["rememberkey"]) && isset($_COOKIE["rememberuser"]))) {
	return;
}

//get user
$rememberuser = preg_replace("/[\W]/","",$_COOKIE["rememberuser"]);
$rememberkey = $_COOKIE["rememberkey"];

//sql stuff
$conn = mysqli_connect("localhost","website",parse_ini_file("/var/www/php/pass.ini")["mysql"]);
mysqli_select_db($conn,"userdata");
$query = sprintf(
	'select rememberuser from remember where rememberuser="%s" and rememberkey="%s"',
	mysqli_real_escape_string($conn, $rememberuser),
	mysqli_real_escape_string($conn, $rememberkey)
);
//get result of query
$res = mysqli_query($conn,$query);
//count rows of query
$rows = mysqli_num_rows($res);
//close sql
mysqli_close($conn);
//after all sql, start session and go
if ($rows !== 0) {
	$_SESSION["username"] = strtolower($rememberuser);
}