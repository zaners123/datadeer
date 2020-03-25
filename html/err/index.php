<!DOCTYPE html>
<html>
<head>
<title><?php
	//default to 404
	if (!isset($_GET["e"])) $_GET["e"]="404";

	echo $_GET["e"]?></title>
</head>
<body>
<h1>Oh Deer, you have an error!</h1>
<a href="https://http.cat"><img alt="cat" src="/err/<?php echo $_GET["e"]?>.jpg"></a>
</body>
</html>