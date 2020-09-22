<?php require "/var/www/php/header.php"; ?>
	<title>DogNet</title>
<?php require "/var/www/php/bodyTop.php"; ?>
<h1>DogNet</h1>
<h2>The DataDeer.net Artificial Intelligence</h2>
<p>
	Upload any image, and this will tell you if it has a dog in it, with 90% accuracy!
</p>
<p>
	This is powered by one of the world's most powerful dog-finding Artificial Intelligence, which lives inside of DataDeer.net
</p>

<p>
	For best confidence, make sure the dog takes up most of the image, and the image has no filters (black & white, etc).
</p>

<p>
	If you give me dog photos, I'll give you 25 <a href="/deercoin">DeerCoins</a>!
	For this, the photos better be taken by you or I may revoke the coins.
	If you have dog photos that you took that don't get counted (it has to be above 60% certainty),
		then email "admin @ (this site)" dog photos and I'll give you DeerCoins.
</p>

<form enctype="multipart/form-data" method="post">
	<table align="center" style="border: 2px solid black">
		<tr>
			<td>
				<input name="userfile" type="file" required><br>
			</td>
		</tr>
		<tr>
			<td>
				<input type="submit" value="Upload">
			</td>
		</tr>
	</table>
</form>

<?php
if (isset($_FILES["userfile"])) {
	require "doglib.php";
	echo "<h1>".getUsersafe()."</h1>";
}?>

<p>
	If you want to put this on your site, feel free to <a href="api.php">Use the API</a>!
</p>

<?php require "/var/www/php/footer.php";?>
