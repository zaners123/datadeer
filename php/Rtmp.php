<?php
require_once "Service_Auth.php";
class Rtmp {
	public static function verifyPassedCreds() {
		if (isset($_POST['username'])){
			$username= filter_input(INPUT_POST, 'username');
		} else if (isset($_POST['name'])){
			$username= filter_input(INPUT_POST, 'name');
		} else {
			return self::replyToRequest(false);
		}
		if (isset($_POST['creds'])) {
			$password = filter_input(INPUT_POST, 'creds');
		} else {
			return self::replyToRequest(false);
		}
		return self::verifyPassedCredsArgs($username,$password);
	}

	public static function verifyPassedCredsArgs($username,$password) {
		$username = Service_Auth::getSingleton()->isRemembered($username,$password);
		self::replyToRequest($username);
		return $username;
	}

	/**
	 * Called when a user wants to publish
	*/
	public static function onPublish() {
		//todo allow each user a max of one stream at once
		error_log("onPublish -- ".json_encode($_POST));
		self::verifyPassedCreds();
		self::replyToRequest(true);
	}

	/**
	 * Called when user wants to view RTMP stream
	 * @example rtmp://datadeer.net:51818/videochat/deer?username=rememberuser&creds=rememberkey
	*/
	public static function onPlay() {
		error_log("onPlay -- ".json_encode($_POST));
		self::verifyPassedCreds();
		self::replyToRequest(true);
	}

	/**
	 * Called when publishing OR playing ends
	 */
	public static function onDone() {
		error_log("onDone -- ".json_encode($_POST));
		self::verifyPassedCreds();
		self::replyToRequest(true);
	}

	/**
	 * Returns either a 401 or 200 based on input
	 * @param $allow bool - Whether or not to allow the request
	 * @return bool returns allow
	 */
	public static function replyToRequest(bool $allow) {
		http_response_code($allow?200:401);
		if (!$allow) exit("ACCESS DENIED UwU");
		return $allow;
	}

}