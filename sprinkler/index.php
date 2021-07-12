<?php

require_once "/var/www/php/sprinklerLib.php";
$h = array("Location","Content-Type");
foreach ($h as $head) {
    header($head.":",true);
    header_remove($head);
}

$id = $_GET["c"];
if (isset($id) && isValidID($id)) {
    printStations($id);
} else {
    exit("BADCODE");
}


