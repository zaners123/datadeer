<?php

/**It should be worth noting, please DO NOT FUCK WITH THIS FILE
 * It is the thing in charge of user authentication and access to locations.
 This can be called multiple times. */
require_once "httpHeader.php";
//If you change this file, check/change phone/phoneStatus.php

require "/var/www/php/startSession.php";

//try to remember user
if (!isset($_SESSION["username"])) {
	require "isRemembered.php";
}

//disable read only sometimes
if (isset($readOnlyDisabled) && isset($_SESSION["readonly"])) {
	http_response_code(403);
	header("Location: /err?e=403");
	die("NO GUEST");
}

//Checks if the session username is not set, empty, or has 0 length.
if (
	//username not set
	!isset($_SESSION["username"])
		||
	//username not alphanumerics
	!preg_match("/^\w+$/",$_SESSION["username"])
) {
    //Redirects you to index
    http_response_code(401);
    header("Location: /");
    //DOESN'T LOAD THE REST OF THE PAGE; THIS IS VITAL FOR HIDING CHAT, UI, ETC.
    die("DENIED - SIGN IN");
}