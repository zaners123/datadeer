<?php
$readOnlyDisabled = true;
require "/var/www/php/requireSignIn.php";?>
<!DOCTYPE html>
<head>
	<title>Settings</title>
	<link rel="stylesheet" type="text/css" href="/css/css.css"/>
</head>
<?php require "/var/www/php/bodyTop.php";

//main CouchDB settings
require_once "/var/www/php/couch.php";
$doc = getDoc("profile",$_SESSION["username"],array());
?>
	<h1>User Settings - Edit your User Portfolio</h1>
	<form method="post" action="/user/userBackend.php">
		<label>Biography<br><textarea rows="4" cols="30" name="biography"><?=urldecode($doc["biography"]);?></textarea></label><br>
		<input type="submit" value="Change Bio">
	</form>
	<br><br>
	<form enctype="multipart/form-data" method="post" action="/user/userBackend.php">
		<label>Change Icon (png,jpg)<br><input name="icon" type="file"></label><br>
		<input type="hidden" name="isicon" value="yes">
		<input type="submit" value="Change Icon">
	</form>
	<h1 style="text-align: center">Visual Settings (not applied on this screen)<br><br></h1>
    <form name="settings" method="post" action="settings.php">
	    <input type="submit" value="Apply Changes"><br><br>

	    DataDeer theme music? Whoa!
	    <br><label class="switch"><input id="music" class="colorbox settingsBox" name="music" type="checkbox" placeholder="music"><span class="slider"> </span></label><br><br>

	    OwO the site! Heck Yeah!
	    <br><label class="switch"><input id="owoify" class="colorbox settingsBox" name="owoify" type="checkbox" placeholder="owoify"><span class="slider"> </span></label><br><br>

	    <!--The game letter switch-->
	    Use Letters - Turn this on if you can't see the queen, checker, or X: "&#9813;&#9920;&#10007;"
	    <br><label class="switch"><input id="gameletter" class="colorbox settingsBox" name="gameletter" type="checkbox" placeholder="gameletter"><span class="slider"> </span></label><br><br>


	    Dark Mode - The site is great at night!
	    <br><label class="switch"><input id="darkmode" class="colorbox settingsBox" name="darkmode" type="checkbox" placeholder="darkmode"><span class="slider"> </span></label><br><br>


	    Motion Sickness - Do you love nausea? Do you want to feel sick always?? If so, turn this on and every page will slowly rotate
	    <br><label class="switch"><input id="sickness" class="colorbox settingsBox" name="sickness" type="checkbox" placeholder="sickness"><span class="slider"> </span></label><br><br>

	    Do you, like the <a href="https://reddit.com/r/datadeer">DataDeer subreddit</a> want a pink background? Turn this on
	    <br><label class="switch"><input id="background" class="colorbox settingsBox" name="background" type="checkbox" placeholder="background"><span class="slider"> </span></label><br><br>

	    Do you want to make your mouse a deer? TURN IT ON!
	    <br><label class="switch"><input id="mouse" class="colorbox settingsBox" name="mouse" type="checkbox" placeholder="mouse"><span class="slider"> </span></label><br><br>

	    Random Color?
	    <br><label class="switch"><input id="randomback" class="colorbox settingsBox" name="randomback" type="checkbox" placeholder="randomback"><span class="slider"> </span></label><br><br>

	    <!--The blocked users CSL-->
	    <!--Blocked Users - List people you don't want to receive messages from here, comma separated. An example is "Fred, Joey".
	    <input id="blocked" name="blocked" type="text" value="" placeholder="Blocked Users"><br>-->
	    <br>
	    <input type="hidden" name="apply" value="true">
	    <input type="submit" value="Apply Changes">
    </form>

	<hr><hr>

	Advanced settings:<br><br>

	<p>Change Password</p> <button>Ask admin</button>
	<p>Forget Remembered Devices</p> <button>Ask admin</button>
	<p>Delete Account</p> <button>Ask admin</button>
<script>
	<?php
	$settings = ["darkmode","gameletter","sickness","background","mouse","randomback","owoify","music"];

	//set settings
	foreach ($settings as $s) {
		if (isset($_POST["apply"]) && $_POST["apply"]==="true") {
			$doc[$s] = isset($_POST[$s])?"true":"false";
		}
		if (isset($doc[$s])) echo "document.getElementById(\"$s\").checked = ".($doc[$s]).";\n";
	}

	setDoc("profile",$_SESSION["username"],$doc);

	?>
</script>
</body>
<?php require "/var/www/php/footer.php"; ?>
