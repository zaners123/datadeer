<?php require "/var/www/php/header.php"; ?>
	<title>Share</title>
<?php require "/var/www/php/bodyTop.php"; ?>
<!--<img src="asset/deer-share.png" alt="Share">-->
	<h1>DataDeer Share Upload</h1>
<?php require "aside.html"; ?>
	<form enctype="multipart/form-data" method="post" action="submit.php">
		<table align="center" style="border: 2px solid black">
			<tr>
				<td>
					<input name="userfile" type="file" required><br>
				</td>
			</tr>
			<tr>
				<td>
					<label>Public:
						<input name="public" class="bigbox" type="checkbox"><br>
					</label>
				</td>
				<tr>
				<td>
					<input type="submit" value="Upload">
				</td>
			</tr>
		</table>
	</form>
	Become internet famous on a bountiful platform.<br>
	Upload images, videos, games, apps, scripts, 3d things, essays,<br>
	documentaries, whatever you want, as long as it's legal.<br>
	<br><br>
	You have used
<?php

//main inform user of limit usage
require_once "/var/www/php/subdata.php";
$doc = getDoc("share",$_SESSION["username"],$blankDefault);

$spaceUsed = 0;
foreach (sanitiseDoc($doc) as $file) {
	$spaceUsed += $file["size"];
}

//20GB or 50MB
$maxBits = isSubscribed()?1024*1024*1024*20:1024*1024*50;

//used percent
echo round($spaceUsed/$maxBits * 100,2);
echo "% of your limit ";
//main bar of space used
echo '<meter min="0" max="'.$maxBits.'" low="33" high="66" optimum="0" value="'.$spaceUsed.'">You don\'t support meter</meter><br>';

//used bytes
echo ($spaceUsed)."/".($maxBits);
echo " bytes<br>";

//based off of Jeffrey Sambells
function toReadableFilesize($bytes) {
	$size   = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
	$factor = (int)floor((strlen($bytes) - 1) / 3);
	return round($bytes / pow(1024, $factor),2).$size[$factor];
}

//main list uploaded files
echo "Uploaded Files:<br>";
foreach (array_reverse(sanitiseDoc($doc)) as $path => $file) {?>
	<a href='https://datadeer.net/share/d.php?n=<?=urlencode($file["name"])?>&q=<?=$path?>'>&#x1f4e5; <?=$file["name"]?> <?=toReadableFilesize($file["size"])?></a>
	&emsp;
	<a class=\"black\" href='https://datadeer.net/share/remove.php?q="<?=$path?>"'>&#x1f5d1;</a><br>
<?php } ?>

<br>
You are limited to 50MB files and 100MB total, unless you have <b>DataDeer Gold</b>.<br>
With DataDeer Gold, you are limited to 250MB files and 20GB total.<br>
Anything users upload can be deleted by allowed server administrators at any time for any reason.<br>

<?php require "/var/www/php/footer.php"; ?>