<?php
/**
Site key - Use this in the HTML code your site serves to users.
Secret key - Use this for communication between your site and Google. Be sure to keep it a secret.
 */

//print_r($_POST);
//if they sent php data for submission don't try parsing sent data
if (!(isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["passwordVerify"]) && isset($_POST["g-recaptcha-response"]))) {
    //echo "Please fill all fields";
    return;
}

if(strlen($_POST["username"]) < 4 || strlen($_POST["username"]) > 30) {
	echo "Username length invalid, needs to be 4-30 characters.";return;
}

if (strtolower($_POST["username"])==="wisely") {
	echo "Wow. Hilarious.";
	return;
} else if (strtolower($_POST["username"])==="changed later") {
	echo "Oh good one. Please use another name.";
	return;
} else if (strtolower($_POST["username"])==="another name") {
	echo "Okay that's enough";
	header("Location: /secret");
	return;
}

//if the passwords do not match tell the user
if ($_POST["password"]!==$_POST["passwordVerify"]) {
    exit("Passwords do not match, please resubmit");
}

//verify the username and password meet minimum/maximum requirements (username max 32, password max 7000)
if(strlen($_POST["password"]) < 4 || strlen($_POST["password"]) > 7000) {
    exit("Password length invalid, needs to be 4-7000 characters.");
}

if ($_POST["username"] !== preg_replace("/[\W]/","",$_POST["username"])) {
    exit("Usernames can only contain a-z, A-Z, 0-9, and _");
}

if (!preg_match("/^\w{4,30}$/",$_POST["username"])) {
	exit("Usernames can only contain a-z, A-Z, 0-9, and _");
}

//echo "Captcha starting";
//Check captcha
$verifyReCaptcha = file_get_contents("https://www.google.com/recaptcha/api/siteverify",false,stream_context_create(
    array(
        'http' => array (
            'method' => 'POST',
            'content' => http_build_query(
                array (
                    'secret' => parse_ini_file("/var/www/php/pass.ini")["recapcha"],
                    'response' => $_POST["g-recaptcha-response"]
                )
            )
        )
    )
));
$verifyResult = json_decode($verifyReCaptcha);
//echo $verifyReCaptcha;
if ($verifyResult->success != true) {
    echo "Captcha Incorrect";return;
}

//echo "captcha correct, good job person! I am making you an account, good person.";
//TODO Table doesn't already contain username (if so tell them so they choose different username)
//make account


$conn = mysqli_connect("localhost","website",parse_ini_file("/var/www/php/pass.ini")["mysql"]);
mysqli_select_db($conn,"userdata");
$query = sprintf(
    'insert into accounts (username,passwordslow) values ("%s","%s")',
    mysqli_real_escape_string($conn, $_POST["username"]),
    mysqli_real_escape_string($conn, password_hash($_POST["password"], PASSWORD_DEFAULT))
);
mysqli_query($conn,$query);
mysqli_close($conn);

/*

$pass = password_hash("secret", PASSWORD_DEFAULT);
echo "my pass is ".$pass;

if (password_verify("secret", $pass)) {
	echo "\nmatches";
} else {
	echo "\nwrong";
}

*/

//signs them in after making their account
require "/var/www/html/signin.php";