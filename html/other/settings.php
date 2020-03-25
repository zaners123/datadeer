<?php
$readOnlyDisabled = true;
require "/var/www/php/requireSignIn.php";?>
<!DOCTYPE html>
<html lang="en">
<head lang="en">
	<title>Settings</title>
	<link rel="stylesheet" type="text/css" href="/css/css.css"/>
	<?php require "/var/www/php/bodyTop.php"; ?>
<h1 style="text-align: center">
	    Settings (not applied on this screen)<br><br>
	    <form name="settings" method="post" action="settings.php">
		    <input type="submit" value="Apply Changes"><br><br>

<!--		    OWO heck yeah-->
		    OwO the site!!!<br>
		    <label class="switch"><input id="owoify" class="colorbox settingsBox" name="owoify" type="checkbox" placeholder="owoify"><span class="slider"> </span></label><br>
		    <br>

		    <!--The game letter switch-->
		    Use Letters - Turn this on if you can't see the queen, checker, or X: "&#9813;&#9920;&#10007;"
		    <br>
		    <label class="switch"><input id="gameletter" class="colorbox settingsBox" name="gameletter" type="checkbox" placeholder="gameletter"><span class="slider"> </span></label><br>
		    <br>


		    Dark Mode - The site is great at night!<br>
		    <label class="switch"><input id="darkmode" class="colorbox settingsBox" name="darkmode" type="checkbox" placeholder="darkmode"><span class="slider"> </span></label><br>
			<br>


		    Motion Sickness - Do you love nausea? Do you want to feel sick always?? If so, turn this on and every page will slowly rotate<br>
		    <label class="switch"><input id="sickness" class="colorbox settingsBox" name="sickness" type="checkbox" placeholder="sickness"><span class="slider"> </span></label><br>
		    <br>

		    Do you, like the <a href="https://reddit.com/r/datadeer">DataDeer subreddit</a> want a pink background? Turn this on<br>
		    <label class="switch"><input id="background" class="colorbox settingsBox" name="background" type="checkbox" placeholder="background"><span class="slider"> </span></label><br>
		    <br>

		    Do you want to make your mouse a deer? TURN IT ON!<br>
		    <label class="switch"><input id="mouse" class="colorbox settingsBox" name="mouse" type="checkbox" placeholder="mouse"><span class="slider"> </span></label><br>
		    <br>

		    Random Color?<br>
		    <label class="switch"><input id="randomback" class="colorbox settingsBox" name="randomback" type="checkbox" placeholder="randomback"><span class="slider"> </span></label><br>
		    <br>

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

    </h1>
<script>
	<?php

	//main CouchDB settings
	require_once "/var/www/php/couch.php";
	$doc = getDoc("profile",$_SESSION["username"],$blankDefault);

	//set settings
	if (isset($_POST["apply"]) && $_POST["apply"]==="true") {
		//darkmode
		$doc["darkmode"] = isset($_POST["darkmode"])?"true":"false";
		//gameletter
		$doc["gameletter"] = isset($_POST["gameletter"])?"true":"false";
		//sickness
		$doc["sickness"] = isset($_POST["sickness"])?"true":"false";
		//make background pink
		$doc["background"] = isset($_POST["background"])?"true":"false";
		//mouse
		$doc["mouse"] = isset($_POST["mouse"])?"true":"false";
		//randomback
		$doc["randomback"] = isset($_POST["randomback"])?"true":"false";
		//owoify (full buffer)
		$doc["owoify"] = isset($_POST["owoify"])?"true":"false";
	}

	//save settings
	setDoc("profile",$_SESSION["username"],$doc);

	//show the settings on the screen
	if (isset($doc["darkmode"])) echo "document.getElementById(\"darkmode\").checked = ".($doc["darkmode"]).";\n";
	if (isset($doc["gameletter"])) echo "document.getElementById(\"gameletter\").checked = ".($doc["gameletter"]).";\n";
	if (isset($doc["sickness"])) echo "document.getElementById(\"sickness\").checked = ".($doc["sickness"]).";\n";
	if (isset($doc["background"])) echo "document.getElementById(\"background\").checked = ".($doc["background"]).";\n";
	if (isset($doc["mouse"])) echo "document.getElementById(\"mouse\").checked = ".($doc["mouse"]).";\n";
	if (isset($doc["randomback"])) echo "document.getElementById(\"randomback\").checked = ".($doc["randomback"]).";\n";
	if (isset($doc["owoify"])) echo "document.getElementById(\"owoify\").checked = ".($doc["owoify"]).";\n";
	//TODO gameletter in game and sickness on php/headerNoSignin
	?>
</script>
<?php require "/var/www/php/footer.php"; ?>
