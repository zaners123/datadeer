<!DOCTYPE html>
<html>
<head>
<?php
$err = filter_input(INPUT_GET,"e",FILTER_VALIDATE_INT);
if (!$err) $err=http_response_code();
?>
<title><?=$err?></title>
</head>
<body style="text-align: center">
<h1>Oh Deer, you have an error!</h1>
<h2>
	<?php if ($err==403) {
		echo "Odds are if you're here, it's because you clicked too fast!
	If you load more than one page per second, the site gets scared!
	Wait 5 seconds, then refresh the page to continue.";
	} else if ($err==404) {
		echo "Awwww page not found. Lemme know if a link broke.<br>";
	}?>
</h2>
<a href="https://http.cat"><img alt="cat" src="/err/<?=$err?>.jpg"></a>
</body>
</html>