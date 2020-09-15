<?php
/**The functions used by CouchDB. This should be the ONLY TIME they are curl-ed.*/

//blank for game matches
$blankDefault = array(
	'x'=>'x'
);

//main removes the _id and _ref variables from the JSON
function sanitiseDoc($doc) {
	foreach ($doc as $key => $value) {
		//stops couchDB variables and getDoc defaults
		if ($key[0] === '_' || $key === 'x') {
			unset($doc[$key]);
		}
	}
	return $doc;
}
//main get database
function getDatabase($folder) {
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, 'http://127.0.0.1:5984/'.rawurlencode($folder)."/_all_docs");
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_USERPWD, parse_ini_file("/var/www/php/pass.ini")["couch"]);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
	$ret = curl_exec($curl);
	curl_close($curl);
	return json_decode($ret, true);
}
//main get any document, but don't check if it exists
function getDocUnsafe($folder,$loc) {
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, 'http://127.0.0.1:5984/'.rawurlencode($folder)."/".rawurlencode($loc));
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_USERPWD, parse_ini_file("/var/www/php/pass.ini")["couch"]);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
	$ret = curl_exec($curl);
	curl_close($curl);
	return $ret;
}
//main get any doc, but if it doesn't exist, make it and set it to default
function getDoc($folder,$loc = null,$default = null) {

	if ($loc==null) $loc=strtolower($_SESSION["username"]);
	if ($default==null) $default=array('x'=>'x');

	$loc = strtolower($loc);

	$response = getDocUnsafe($folder,$loc);
	$response = json_decode($response, true);
	if (isset($response["error"]) && ($response["error"]==="not_found" || $response["error"]==="bad_request")) {
		setDoc($folder, $loc, $default);
		return json_decode(getDocUnsafe($folder,$loc), true);
	}
	return $response;
}
//main set any doc. The doc should be gotten first.
function setDoc($folder,$loc,$doc) {

	$loc = strtolower($loc);

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, 'http://127.0.0.1:5984/'.rawurlencode($folder)."/".rawurlencode($loc));
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($doc));
	curl_setopt($curl, CURLOPT_USERPWD, parse_ini_file("/var/www/php/pass.ini")["couch"]);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
	$ret = curl_exec($curl);
	curl_close($curl);
	return $ret;
}