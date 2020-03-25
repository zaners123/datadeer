<?php
/**
 *
 * This is called when the user signs in OR makes an account
 * It checks the SQL table to see if it has the username/password as a row
 * If so it adds the username as a session variable
 *
 */

//after this output, always take them to signin.php
header("Location: /");
require "/var/www/php/lib.php";


//main owo secrets
if (isset($_POST["username"]) && $_POST["username"]=="furry") {
	if (session_status() == PHP_SESSION_NONE) session_start();
	$_SESSION["furry"]="furry";
	header("Location: /other/furry.php");
	exit("Go here");
}

//main guest account logic
//if (isset($_POST["guest"]) && $_POST["guest"]=="Sign In as Guest") {
//	guest signin
//	signIn(null,"guest","no");
//	$_SESSION["readonly"] = "readonly";
//	die("Signing you in");
//}

//main user input filtering
if (!(isset($_POST["username"]) || isset($_POST["password"]))) {die("DENIED");}
if (!isset($_POST["remember"])) $_POST["remember"] = "no";
$_POST["username"] = preg_replace("/[\W]/","",$_POST["username"]);
if(strlen($_POST["password"]) > 7000) {
	die("DENIED");
}

//try new sign in
$conn = mysqli_connect("localhost","website",parse_ini_file("/var/www/php/pass.ini")["mysql"], "userdata");
$newQuery = sprintf(
	'select * from accounts where username="%s"',
	mysqli_real_escape_string($conn, $_POST["username"])
);
$newRes = mysqli_query($conn,$newQuery);

if (!$newRes) {
	securityLog("Failed new Login");
	die("DENIED");
}

$row = mysqli_fetch_assoc($newRes);

if (!$row) {
	securityLog("Failed new Login");
	die("DENIED");
}

if (password_verify($_POST["password"], $row["passwordslow"])) {

	//main passed secure sign in
	signIn($conn,$_POST["username"],$_POST["remember"]);
	securityLog("Successful sign in using secure method");
} else {
	securityLog("Failed new Login, trying old login");
	$oldQuery = sprintf(
		'select username from accounts where username="%s" and password="%s"',
		mysqli_real_escape_string($conn, $_POST["username"]),
		mysqli_real_escape_string($conn, hash("sha512",$_POST["password"]))
	);
	$oldRes = mysqli_query($conn,$oldQuery);
	$oldRows = mysqli_num_rows($oldRes);
	if ($oldRows === 0) {
		//main no rows in old login
		securityLog("Failed old login, too");
		die("DENIED");
	} else {
		//main sign in after succeeding old login for last time
		signIn($conn,$_POST["username"],$_POST["remember"]);
		securityLog("Succeeded SHA512 login; make password_verify login");
		$query = sprintf(
			'replace into accounts (username,password,passwordslow) values ("%s",NULL,"%s")',
			mysqli_real_escape_string($conn, $_POST["username"]),
			mysqli_real_escape_string($conn, password_hash($_POST["password"], PASSWORD_DEFAULT))
		);
		mysqli_query($conn,$query);
	}
}
/**
 * @param mysqli conn - A mysqli conn used for applying $remember
 * @param string username - the $_POST["username"] or guest
 * @param string remember - "on" for yes, anything else for no
 * @return boolean true if it could make a remember cookie
 */
function signIn($conn, $username, $remember) {
	//necessary for signing in
	session_start();
	//double check its lowercase
	$_SESSION["username"] = strtolower($username);

	securityLog("Successful sign in");

	//main make cookies if you chose "Remember Me"
	if (isset($remember) && $remember==="on") {
		//if you already have a "remember me" key
		$oldQuery = sprintf(
			'select * from remember where rememberuser="%s"',
			mysqli_real_escape_string($conn, $username)
		);
		$oldRes = mysqli_query($conn,$oldQuery);
		$oldRows = mysqli_num_rows($oldRes);
		//main if "remember me" key doesn't exist, generate it
		if ($oldRows === 0) {
			//generate cryptographically random key for remembering sign in. Key is stored on client and server for authentication
			$keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$keyspacelen = strlen($keyspace);
			$rememberlength = 64;
			$rememberkey = "";
			try {
				for ($i = 0; $i < $rememberlength; ++$i) {
					$rememberkey .= $keyspace[random_int(0, $keyspacelen-1)];
				}
			} catch (Exception $e) {
				error_log("NO ENTROPY FOR REMEMBER COOKIE");
				return false;
			}
			//store rememberUser and rememberKey in MYSQL
			$oldQuery = sprintf(
				'replace into remember (rememberuser,rememberkey) value ("%s","%s")',
				mysqli_real_escape_string($conn, $username),
				mysqli_real_escape_string($conn, $rememberkey)
			);
			mysqli_query($conn,$oldQuery);
//			error_log("MAKING REMEMBERKEY".$rememberkey);
		} else {
			//rememberme key exists, use the key that already exists
			$rememberkey = mysqli_fetch_assoc($oldRes)["rememberkey"];
//			error_log("ALREADY HAS REMEMBERKEY ".$rememberkey);
		}

		//set cookies
		$expire = time()+60*60*24*365*10;//about 10 years
		setcookie("rememberuser",$username, $expire);
		setcookie("rememberkey", $rememberkey,  $expire);
		return true;
	}
	return true;
}