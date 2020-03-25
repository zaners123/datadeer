<?php
require_once "/var/www/html/gold/subdata.php";
require "requireSignIn.php";
//maybe call isUserSubscribed because it is slightly more accurate than isSubscribed
if (!isSubscribed()) {
	//Redirects you to index
	http_response_code(402);
	header("Location: /");
	//DOESN'T LOAD THE REST OF THE PAGE; THIS IS VITAL FOR HIDING CHAT, UI, ETC.
	die("DENIED - SIGN IN");
	return;
}