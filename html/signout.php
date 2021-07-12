<?php

//log file
require "/var/www/php/sprinklerLib.php";
securityLog("User Signing Out");

//sign out
require "/var/www/php/startSession.php";
$_SESSION = array();
session_destroy();

//delete "Remember Me" cookies (or else it keeps signing them in...)
setcookie("rememberuser","",time()-3600);
setcookie("rememberkey","",time()-3600);

//redirect to sign in page to show they are signed out.
header("Location: /");