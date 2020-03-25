<?php require "/var/www/php/header.php" ?>
	<title>About you</title>
<?php require "/var/www/php/bodyTop.php"; ?>
<h1>About you:<br>
<?php echo "Your IP: ".$_SERVER['REMOTE_ADDR'].":".$_SERVER['REMOTE_PORT'];?><br>
</h1>
<?php require "/var/www/php/footer.php" ?>