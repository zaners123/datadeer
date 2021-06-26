<?php
header_remove("Location");
if (!isset($_GET["c"])) {
	echo "Set c with code, such as ?c=1234567890";
	return;
}
echo "Sprinkler code ".$_GET["c"];
