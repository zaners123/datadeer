<?php
/**The backend used by finance programs to:
 *      save and load finances
 * The backend used by to do to:
 *      save and load notes
 */

require "/var/www/php/requireSignIn.php";
require_once "/var/www/php/couch.php";
$def = array(
	'x' => 'x'
);
//main get the doc then apply the changes
$doc = getDoc("todo",$_SESSION["username"],$def);

//main make changes if requested
if (isset($_GET["remrow"])) {
	//main remove that row from the table
	unset($doc[$_GET["remrow"]]);

	setDoc("todo",$_SESSION["username"],$doc);
} else if (isset($_GET["title"])) {
	$id = uniqid();
	//main add that table to the TO DO DB
	$doc[$id] = array(
		"id"=>$id,
		"time"=>isset($_GET["time"])?strtotime($_GET["time"]):time(),
		"title"=>isset($_GET["title"])?$_GET["title"]:""
	);

	setDoc("todo",$_SESSION["username"],$doc);
}
//main return the encoded cod
echo json_encode($doc);