<?php
$readOnlyDisabled = true;
require "/var/www/php/requireSignIn.php";
require "../chatFunc.php";
/**

 * grant select on userdata.pchat to 'website'@'localhost';
 * grant insert on userdata.pchat to 'website'@'localhost';

 */

/**This file:
 *      Reads Private SQL Chatrooms, returning the results
 *      Adds chat to Private SQL Chatrooms, returning the results
 * */
if (isset($_GET["user"])) {
	if (strlen($_GET["user"]) < 4) return;
	if (isset($_GET["msg"])) {
		//sending chat (return results)
		addChat($_SESSION["username"], $_GET["user"], $_GET["msg"]);
		echo readChat($_SESSION["username"], $_GET["user"]);
	} else {
		//reading chat
		echo readChat($_SESSION["username"], $_GET["user"]);
	}
}

