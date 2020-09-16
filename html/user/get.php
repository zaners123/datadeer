<?php
/**
Return that user's profile
 */
require_once "/var/www/php/couch.php";
if (!isset($_GET["user"])) return false;
echo json_encode(sanitiseDoc(getDoc("profile",strtolower($_GET["user"]),array(
	"username"=>$_GET["user"],
	"biography"=>"",
	//a 160x160 PNG, blank by default
	"icon"=>"",
))));