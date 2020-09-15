<?php

class Service_Auth {

	private $db = null;

	private static $sing = null;

	function __construct() {

	}

	/**
	 * Alias for getSingleton
	 * @return Service_Auth
	 */
	static function sing() {
		return self::getSingleton();
	}
	/**
	 * @return Service_Auth
	 */
	static function getSingleton() {
		if (self::$sing != null) return self::$sing;
		return self::$sing = new Service_Auth();
	}

	/**
	 * @param string $database The Database
	 * @return mysqli
	 */
	function getDB(string $database = 'userdata') {
		if ($this->db != null) {
			$this->db->mysqli_select_db($database);
			return $this->db;
		}
		return $db = mysqli_connect("localhost","website",parse_ini_file("/var/www/php/pass.ini")["mysql"],$database);
	}

	/**
	 * Returns true if user is remembered
	 * @param string $user string rememberuser cookie
	 * @param string $key string rememberkey cookie
	 * @return string|null
	 */
	function isRemembered(string $user,string $key) {
		$user = preg_replace("/[\W]/","",$user);
		$conn = $this->getDB("userdata");
		$res = mysqli_query($conn,sprintf(
			'select rememberuser from remember where rememberuser="%s" and rememberkey="%s"',
			mysqli_real_escape_string($conn, $user),
			mysqli_real_escape_string($conn, $key)
		));
		$rows = mysqli_num_rows($res);
		mysqli_close($conn);
		if ($rows !== 0) {
			return $user;
		} else {
			return null;
		}
	}

	/**
	 * Tries remembering user with cookies and signing them in
	 */
	function tryRememberingUser() {
		if (!array_key_exists("rememberuser",$_COOKIE)||!array_key_exists("rememberuser",$_COOKIE)) return;
		$user = $_COOKIE["rememberuser"];
		$key = $_COOKIE["rememberkey"];
		if ($this->getUser()!=null) return;
		$user = $this->isRemembered($user,$key);
		if ($user) {
			$this->setUser($user);
		}
	}

	/**Filters username to an allowed username. Returns valid username, or null on error
	 * @param string $user
	 * @return string|null
	 */
	static function filterUsername(string $user) {
		if ($user==null) return null;
		$user = preg_replace("/[\W]/","",strtolower($user));
		if (is_array($user)) return null;
		if (preg_match('/[\W]/',$user)) return null;
		if (strlen($user) < 4 || strlen($user)>30) return null;
		return $user;
	}

	/**Filters password to an allowed password. Returns valid password, or null on error
	 * @param string $password
	 * @return string|null
	*/
	private static function filterPassword(string $password) {
		if ($password==null) return null;
		if (strlen($password) < 4 || strlen($password) > 7000) return null;
		return $password;
	}

	static function setUser($user) {
		if ($user==null) return;
		$_SESSION["username"] = self::filterUsername($user);
	}

	static function getUser() {
		if (array_key_exists("username",$_SESSION)) {
			return $_SESSION["username"];
		} else {
			return null;
		}
	}

	function setRememberMeKey($username) {
		$conn = $this->getDB();
		$oldRes = mysqli_query($conn,sprintf(
			'select * from remember where rememberuser="%s"',
			mysqli_real_escape_string($conn, $username)
		));
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
			mysqli_query($conn,sprintf(
				'replace into remember (rememberuser,rememberkey) value ("%s","%s")',
				mysqli_real_escape_string($conn, $username),
				mysqli_real_escape_string($conn, $rememberkey)
			));
		} else {
			$rememberkey = mysqli_fetch_assoc($oldRes)["rememberkey"];
		}
		$expire = time()+60*60*24*365*10;//about 10 years
		setcookie("rememberuser",$username, $expire);
		setcookie("rememberkey", $rememberkey,  $expire);
		return true;
	}

	/**
	 * @param string $username
	 * @return bool - iff username exists
	 */
	function userExists(string $username) {
		$username = self::filterUsername($username);
		if ($username==null) return false;
		return mysqli_num_rows($this->getDB('userdata')->query(sprintf(
			"select username from accounts where username='%s'",
			$username
		))) > 0;
	}

	/**
	 * Makes a new user account
	 * @param string $username
	 * @param string $password
	 * @return bool
	 */
	function createAccount(string $username, string $password) {
		$username = self::filterUsername($username);
		$password = self::filterPassword($password);
		if ($username==null || $password==null) return false;
		if ($this->userExists($username)) return false;
		$conn = $this->getDB('userdata');
		$query = sprintf(
			'insert into accounts (username,passwordslow) values ("%s","%s")',
			mysqli_real_escape_string($conn, $_POST["username"]),
			mysqli_real_escape_string($conn, password_hash($_POST["password"], PASSWORD_DEFAULT))
		);
		mysqli_query($conn,$query);
		mysqli_close($conn);
		return true;
	}

	/**The old (still supported for users that haven't signed in in over a year) authentication method
	 * @param string $username
	 * @param string $password
	 * @param bool $remember
	 * @return bool
	 */
	private function authenticateOld(string $username, string $password, bool $remember) {
		$conn = $this->getDB('userdata');
		$oldQuery = sprintf(
			'select username from accounts where `username`="%s" and `password`="%s"',
			mysqli_real_escape_string($conn, $username),
			mysqli_real_escape_string($conn, hash("sha512",$password))
		);
		$oldRes = mysqli_query($conn,$oldQuery);
		$oldRows = mysqli_num_rows($oldRes);
		if ($oldRows === 0) {
			securityLog("Failed old login, too");
			return false;
		} else {
			securityLog("Succeeded SHA512 login; make password_verify login");
			mysqli_query($conn,sprintf(
				'replace into accounts (username,`password`,passwordslow) values ("%s",NULL,"%s")',
				mysqli_real_escape_string($conn, $username),
				mysqli_real_escape_string($conn, password_hash($password, PASSWORD_DEFAULT))
			));
			return true;
		}
	}

	/**Sees if user can authenticate. Returns yes if they can.
	 * @param string $username
	 * @param string $password
	 * @param bool $remember
	 * @return bool
	 */
	function canAuthenticate(string $username, string $password, bool $remember) {
		$username = self::filterUsername($username);
		$password = self::filterPassword($password);
		if ($username==null || $password==null) return false;
		$conn = $this->getDB('userdata');
		$newRes = mysqli_query($conn,sprintf('select `passwordslow` from accounts where username="%s"',mysqli_real_escape_string($conn, $username)));
		if (!$newRes) return false;
		$row = mysqli_fetch_assoc($newRes);
		if (!$row) return false;
		if (password_verify($password, $row["passwordslow"])) {
			securityLog("Successful sign in using secure method");
			return true;
		} else {
			securityLog("Failed new Login, trying old login");
			return $this->authenticateOld($username,$password,$remember);
		}
	}

	/**
	 * Attempts to authenticate this user. If successful, this signs them in (if you just wanna see if they're legit, use canAuthenticate
	 * This is called when the user signs in OR makes an account
	 * @param string $username user's plaintext username
	 * @param string $password user's plaintext password
	 * @param bool $remember if you wanna set a "remember me" cookie
	 * @return bool true on success
	 */
	function authenticate(string $username, string $password, bool $remember) {
		if ($this->canAuthenticate($username, $password, $remember)) {
			$this->authorize($username,$remember);
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Signs the user in and might set the the "remember me" key
	 * @param string $username their username
	 * @param bool $remember if you wanna give them a "remember me" cookie
	 * @return boolean true if it could make a remember cookie
	 */
	function authorize(string $username, bool $remember) {
		//double check its lowercase
		self::setUser($username);
		if ($remember) {
			return $this->setRememberMeKey($username);
		}
		securityLog("Successful sign in");
		return true;
	}
}