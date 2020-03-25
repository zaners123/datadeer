<?php require "/var/www/php/header.php"; ?>
	<title>Why use a Cookie</title>
<?php require "/var/www/php/bodyTop.php"; ?>
	<h1>Why does DataDeer need a cookie?</h1>
<h3>
	DataDeer.net needs a cookie to remember you are signed in.<br>
	<br>
	When you click "Sign In", the site has to remember who you are multiple times.<br>
	Examples of this are when you send a chat or use most other DataDeer.net services.<br>
	The server then has to know who you are each time you do this.<br>
	Instead of putting in your username and password on everything you do, you just do it at the start, so you can stay signed in.<br>
	<br><br>
	As of 3/22/19, DataDeer uses four cookies.
	<ul>
		<li>Your session (the thing that holds your account, like an ID badge).</li>
		<li>Two for the "Remember Me" feature. It is a long key used so we know it's you.</li>
		<li>A random cookie flavor, and is currently <?php echo $_COOKIE["delicious-cookie"]?></li>
	</ul>
</h3>
<?php require "/var/www/php/footer.php"; ?>