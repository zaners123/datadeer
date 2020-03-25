<?php
/**This file is required at the top of every phone-running file. It returns a line containing the signing-in status
 * It only uses the first line (everything before the first newline)
 * Here are the possible things it could send:
 *      "NEEDCOOKIE" - You didn't even give it a session cookie
 *      "NEEDTOSIGNIN" - You gave it a cookie, but the cookie is not signed in
 *      "SIGNEDIN" - You are signed in. Following this is likely the data you requested
 */

//starts session (thus reading session variables, etc)
//get session data for verifying
session_start();

//try to see if you can authenticate with the isRemembered cookie
if (!isset($_SESSION["username"])) {
	require "/var/www/php/isRemembered.php";
}

//Checks if the session username is not set, empty, or has 0 length.
if (!isset($_COOKIE["PHPSESSID"])) {
	echo "NEEDCOOKIE\n";
	return;
}

//Checks if the session username is not set, empty, or has 0 length.
if (!isset($_SESSION["username"])) {
	//Redirects you to index
	http_response_code(403);
	//header("Location: /");
	//DOESN'T LOAD THE REST OF THE PAGE; THIS IS VITAL FOR HIDING CHAT, UI, ETC.
	die("NEEDTOSIGNIN");
	return;
} else {
	echo "SIGNEDIN\n";
}

//if the user makes it to here or past, they are good