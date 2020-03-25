<?php
/**The backend used by finance programs to:
 *      save and load finances
 * The backend used by to do to:
 *      save and load notes
 */

require "/var/www/php/requireSignIn.php";
require_once "/var/www/php/couch.php";


$fin = array (
	'x'=>'x'
);
//load their finance data from the CouchDB database

//save all of their finance data

//main if the user is deleting their financial data
if (isset($_GET["delete"]) && $_GET["delete"]==="delete my data") {
	$doc = getDoc("finance",$_SESSION["username"],$fin);
	foreach ($doc as $key => $value) {
		if ($key[0] != '_') unset($doc[$key]);
	}
	setDoc("finance",$_SESSION["username"],$doc);
	return;
}

//main if the reason is not set, we are done
if (!isset($_GET["r"])) return;
//main docPart is the table trying to be changed.
if ($_GET["r"]==="s") $docPart = "single";
if ($_GET["r"]==="m") $docPart = "multiple";
if (!isset($docPart)) return;

//main get the doc then apply the changes
$doc = getDoc("finance",$_SESSION["username"],$fin);

//main if that part of the doc doesn't exist, make a blank one
if (!isset($doc[$docPart]) || ($doc[$docPart] instanceof stdClass)) {
	$doc[$docPart] = array();
}

//main for finance add a row to the backend (required fields are set)
if (isset($_GET["remrow"])) {
	//main remove that row from the table
	unset($doc[$docPart][$_GET["remrow"]]);
	setDoc("finance",$_SESSION["username"],$doc);
}else if (($docPart==="single" || $docPart==="multiple") && isset($_GET["category"]) && isset($_GET["money"])) {

	//the id is used as a unique access variable in things like deletion
	$id = uniqid();
	//main add that table to the finance DB
	$doc[$docPart][$id] = array(
		"id" => $id,
		"time" => isset($_GET["time"]) ? strtotime($_GET["time"]) : time(),
		"amount" => $_GET["money"],
		"category" => $_GET["category"],
		"title" => isset($_GET["title"]) ? $_GET["title"] : "",
		"msg" => isset($_GET["desc"]) ? $_GET["desc"] : ""
	);

	setDoc("finance",$_SESSION["username"],$doc);
}
//main return the encoded cod
echo json_encode($doc[$docPart]);