<?php require "/var/www/php/headerNoSignin.php"; ?>
	<title>Edit User Profile</title>
<?php require "/var/www/php/bodyTop.php"; ?>
<h1>Edit your User Portfolio</h1>
<form method="post" action="userBackend.php">
	<label>Biography<br><textarea name="biography"></textarea></label><br>
	<input type="submit" value="Change Bio">
</form>

<br><br>

<form enctype="multipart/form-data" method="post" action="userBackend.php">
	<label>Change Icon (png,jpg)<br><input name="icon" type="file"></label><br>
	<input type="hidden" name="isicon" value="yes">
	<input type="submit" value="Change Icon">
</form>