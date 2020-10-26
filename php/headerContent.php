<?php
//owo entire page here
if (isset($_SESSION["username"])) {
	require_once "/var/www/php/couch.php";
	$doc = getDoc("profile", $_SESSION["username"], array('x'=>'x'));
//	if (true) {
	if (isset($doc["owoify"]) && $doc["owoify"] == "true") {
		$owoify = true;
		function owoify($buffer) {
			$startWith = '/';
			$endWith = '(?![^<]*>)/';
			$buffer = preg_replace($startWith.'wh'                                           .$endWith, "w", $buffer);
			$buffer = preg_replace($startWith.'((\b(dude)|(sir)|(hey)|(wow)|(obama)\b)|(\?))'.$endWith, " OwO ", $buffer);
			$buffer = preg_replace($startWith.'\W(mr)|(mister)\W'                            .$endWith, "mistew", $buffer);
			$buffer = preg_replace($startWith.'\W(cat)|(kitten)|(lion)\W'                    .$endWith, "kitty >:3", $buffer);
			$buffer = preg_replace($startWith.'!'                                            .$endWith, " OWO", $buffer);
			$buffer = preg_replace($startWith.'[lr]'                                         .$endWith, "w", $buffer);
			$buffer = preg_replace($startWith.'[LR]'                                         .$endWith, "W", $buffer);
			$buffer = preg_replace($startWith.'[i]'                                          .$endWith, "y", $buffer);
			return $buffer;
		}
		ob_start("owoify");
	}
}?>
<!DOCTYPE html>
<!--
 This website's code is on github and will accept good/fun forks. If you found this, you're a nerd and should help datadeer development. E-Mail "admin" @ this site if you want to apply your web development skills.
-->
<html lang="en">
<head lang="en">
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta property="og:image" content="https://datadeer.net/datadeer.png">
	<meta name="description" content="The site for all of your needs. Or at least most of them.">
	<meta charset="UTF-8">
	<link rel="stylesheet" type="text/css" href="/css/css.css"/>
<?php
if (isset($_SESSION["username"])) {
	if (isset($doc["background"]) && $doc["background"] == "true") echo '<link rel="stylesheet" type="text/css" href="/css/pink.css"/>';
	if (isset($doc["darkmode"]) && $doc["darkmode"] == "true") echo '<link rel="stylesheet" type="text/css" href="/css/dark.css"/>';
	if (isset($doc["sickness"]) && $doc["sickness"] == "true") echo '<link rel="stylesheet" type="text/css" href="/css/sickness.css"/>';
	if (isset($doc["mouse"]) && $doc["mouse"] == "true") echo '<link rel="stylesheet" type="text/css" href="/css/mouse.css"/>';
	if (isset($doc["randomback"]) && $doc["randomback"] == "true") echo "<style>body {background: rgb(".rand(175,255).",".rand(175,255).",".rand(175,255).");}</style>";
//<script src="/js/snow.js"> </script>
//<link rel="stylesheet" type="text/css" href="/css/christmas.css"/>
}?>