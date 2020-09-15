<?php

if (!(isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["passwordVerify"]) && isset($_POST["g-recaptcha-response"]))) {
    return;
}

if(strlen($_POST["username"]) < 4 || strlen($_POST["username"]) > 30) {
	echo "Username length invalid, needs to be 4-30 characters.";return;
}

if (strtolower($_POST["username"])==="wisely") {
	echo "Wow. Hilarious.";return;
} else if (strtolower($_POST["username"])==="changed later") {
	echo "Oh good one. Please use another name.";return;
} else if (strtolower($_POST["username"])==="another name") {
	header("Location: /secret");
	echo "Okay that's enough";return;
}

//if the passwords do not match tell the user
if ($_POST["password"]!==$_POST["passwordVerify"]) {
    echo "Passwords do not match, please resubmit";return;
}

//verify the username and password meet minimum/maximum requirements (username max 32, password max 7000)
if(strlen($_POST["password"]) < 4 || strlen($_POST["password"]) > 7000) {
    echo "Password length invalid, needs to be 4-7000 characters.";return;
}

if ($_POST["username"] !== preg_replace("/[\W]/","",$_POST["username"])) {
    echo "Usernames can only contain a-z, A-Z, 0-9, and _";return;
}

if (!preg_match("/^\w{4,30}$/",$_POST["username"])) {
	echo "Usernames can only contain a-z, A-Z, 0-9, and _";return;
}
$data = http_build_query(array (
	'secret' => parse_ini_file("/var/www/php/pass.ini")["recapcha"],
	'response' => $_POST["g-recaptcha-response"]
));
$verifyReCaptcha = file_get_contents("https://www.google.com/recaptcha/api/siteverify",false,stream_context_create(
    array(
        'http' => array (
            'method' => 'POST',
	        'header'=> "Content-type: application/x-www-form-urlencoded\r\nContent-Length: " . strlen($data) . "\r\n",
            'content' => $data,
        )
    )
));
$verifyResult = json_decode($verifyReCaptcha);
if ($verifyResult->success != true) {
    echo "Captcha Incorrect";return;
}

//make account
require_once "Service_Auth.php";
if (!Service_Auth::sing()->createAccount($_POST["username"],$_POST["password"])) {
	echo "Account Taken";return;
}


require "/var/www/html/signin.php";
