<?php

if (isset($_GET["subs"]) && $_GET["subs"]=="true") {
	$fileList = glob("/var/www/libs/*");
	foreach ($fileList as $file) {
		if (!is_file($file)) continue;
		echo str_replace("/var/www/libs/","",$file)."\n";
	}
} else if (isset($_GET["page"])) {
	$page = "/var/www/libs/".preg_replace("/[^a-z A-Z]/","",urldecode($_GET["page"]));
	//echo $page."  --  ".is_file($page)."  --  ".file_exists($page)."  --  ";
	echo file_get_contents($page);
} else {
	echo "UNAUTHORIZED ACCESS PROHIBITED";
}