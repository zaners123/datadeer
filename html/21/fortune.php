<?php require "21header.php" ?>
<title>Better Fortunes</title>
<?php require "/var/www/php/bodyTop.php"; ?>
<pre style="font-size: 32px"><?php echo `/usr/games/fortune -os | /usr/games/cowsay`; ?></pre>