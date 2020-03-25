<?php require "/var/www/php/header.php" ?>
<title>Fortune</title>
<?php require "/var/www/php/bodyTop.php"; ?>
<pre style="font-size: 36px"><?php echo `/usr/games/fortune | /usr/games/cowsay`; ?></pre>
<br><br>
Fortunes thanks to <a href="mailto:warp10@ubuntu.com">Andrea Colangelo</a>