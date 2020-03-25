<?php
if (session_status() == PHP_SESSION_NONE) session_start();
if (!($_SESSION["furry"]=="furry")) {
	header("Location: /");
	exit("404");
}
$files = scandir("serg.al");
$filename = $files[rand(2,sizeof($files)-1)];
echo "<img src='serg.al/".$filename."'>";