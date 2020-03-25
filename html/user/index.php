<?php require "/var/www/php/header.php"; ?>
	<title><?php echo $_GET["user"]?> User Profile</title>
<?php require "/var/www/php/bodyTop.php"; ?>
<h1 id="frame" style="text-align: center">
	User Info <?php if (strtolower($_SESSION["username"])===strtolower($_GET["user"])) {echo "<a href='edit.php'>EDIT</a>";}?><br>
	User - <?php echo $_GET["user"]?>
	<hr>
	<span id="icon"></span><br>
	<span id="biography"></span><br>
	<br>
	<a href="/chats/pchat/chatroom.php?user=<?php echo $_GET["user"]?>" id="message">Private Message <?php echo $_GET["user"]?></a><br>
</h1>
<script>
    function urldecode(str) {
        return decodeURIComponent((str+'').replace(/\+/g, '%20'));
    }
	let info = JSON.parse(<?php
		/**
		USERS:
		Make user profiles (user picture, user bio, user posts with comments)
		main so i'm planning on having a user profile and you can edit your user bio, from that add user icon images
		 *
		 * This is done in many files (as are most things on this site):
		 *      user/index.php
		 *          You can view an account in user?q=bob
		 *          Viewing your own account redirects you to edit.
		 *      user/edit.php
		 *          You can edit your account, such as your bio and icon.
		 * The data is in CouchDB in the following format:
		 *      For reading data (index.php):
		 *          Use a variant of getMyDoc where it asks for a username and returns ONLY ["user"]["profile"]
		 *      For writing data (edit.php):
		 *          Use getMyDoc and then setMyDoc as you would TO-DO or Finance
		 *
		 *
		 */
		require_once "/var/www/php/couch.php";
		$prof = array(
			"username"=>$_GET["user"],
			"biography"=>"",
			//a 160x160 PNG, blank by default
			"icon"=>"",
		);
		echo "'".json_encode(sanitiseDoc(getDoc("profile",$_GET["user"],$prof)))."'";?>);
	if (info["err"]==="yeah") {
	    document.getElementById("frame").innerHTML="Unknown User<br>or<br>The User Does Not Have a Profile";
	} else {
        document.getElementById("icon").innerHTML = "<img src=\""+info["icon"]+"\" width=\"200px\" height=\"200px\" alt=\"\" />";
        document.getElementById("biography").innerText = urldecode(info["biography"]);
    }
</script>
<?php require "/var/www/php/footer.php"; ?>