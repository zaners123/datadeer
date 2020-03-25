<?php
header('Content-disposition: attachment; filename=file.json');
header('Content-type: application/json');
require "backend.php";

echo json_encode(sanitiseDoc(json_decode(getDocUnsafe("finance",$_SESSION["username"]),true)),true);
