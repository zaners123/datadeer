<?php

function makeSessionId(){
	return rand(1, 0xFFFFFFFF) & 0x0F0F0F0F;
}
function unpackBasicPort($fp){
	$unpacked = unpack('vport', fread($fp, 2));
	return (string)$unpacked['port'];
}
function getString($fp){
	$string = '';
	while (($lastChar = fread($fp, 1)) !== chr(0)) {
		$string .= $lastChar;
	}
	return $string;
}
function readResponseHeader($fp, $withChallengeToken=false){
	$header = fread($fp, 5);
	$unpacked = unpack('ctype/NsessionId', $header);
	if ($withChallengeToken) {
		$unpacked['challengeToken'] = (int)getString($fp);
	}
	return $unpacked;
}
function validateResponse($response, $type, $sessionId){
	$invalidType = ($response['type'] !== $type);
	$invalidSessionId = ($response['sessionId'] !== $sessionId);
	if ($invalidType || $invalidSessionId) {
		$errorMessage = 'Invalid Response:';
		$errorMessage .= ($invalidType) ? " {$response['type']} !== {$type}" : '';
		$errorMessage .= ($invalidSessionId) ? " {$response['sessionId']} !== {$sessionId}" : '';
		error_log($errorMessage);
		return false;
	}
	return true;
}
function handleHandshake($fp, $sessionId) {
	$handshakeRequest = pack('cccN', 0xFE, 0xFD, 9, $sessionId);
	fwrite($fp, $handshakeRequest);
	$handshakeResponse = readResponseHeader($fp, true);
	if (!validateResponse($handshakeResponse, 9, $sessionId)) {
		return false;
	}
	return $handshakeResponse['challengeToken'];
}

//main make a challenge token
$timeout = 5;
$sessionId = makeSessionId();
$fp = stream_socket_client('udp://127.0.0.1:25565', $errno, $errmsg, $timeout);

if (!$fp) {
	echo "let JSON = \"err\"";
	return;
}


$challengeToken = handleHandshake($fp, $sessionId);
if (!$challengeToken) {
	fclose($fp);
	echo 'let JSON = "bad shake"';
}
//echo "TOKEN".$challengeToken;

//main use the token to get stat states
$statRequest = pack('cccNN', 0xFE, 0xFD, 0, $sessionId, $challengeToken);
fwrite($fp, $statRequest);

$statResponseHeader = readResponseHeader($fp);
if (!validateResponse($statResponseHeader, 0, $sessionId)) {
	fclose($fp);
	echo 'let JSON = "err"';
	return;
}
echo "let JSON = ".json_encode(array (
		'motd'         => getString($fp),
		'gametype'     => getString($fp),
		'map'          => getString($fp),
		'player_count' => getString($fp),
		'player_max'   => getString($fp),
		'port'         => unpackBasicPort($fp),
		'ip'           => getString($fp),
	)).";";
fclose($fp);