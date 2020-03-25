<!DOCTYPE html>
<html>
<head>
<?php
$err = filter_input(INPUT_GET,"e",FILTER_VALIDATE_INT);
if (!$err) $err=http_response_code();
?>
<title><?=$err?></title>
</head>
<body>
<h1>Oh Deer, you have an error!</h1>
<a href="https://http.cat"><img alt="cat" src="/err/<?=$err?>.jpg"></a>
</body>
</html>