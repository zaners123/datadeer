<head>
	<style>
		body{
			background: #000;
		}
		video{
			position: absolute;
			top:0;
			bottom:0;
			left:0;
			right:0;
			max-width:100%;
			max-height:100%;
		}
	</style>
	<title>(Enable Autoplay) Datadeer Livestream</title>
</head>
<body>
<pre><?php
ini_set("allow_url_fopen","On");
require "/var/www/php/flickr.php";
$banned = array(49006233731,48999539877,48994901281,48202608832,49006853032);
do {
	$rand = rand(1, 150);
	$searchResult = searchPhotos("wolf", "videos", $rand, 25);
	$perrand = rand(0,24);
	$vidID = $searchResult[$perrand]["id"];
} while (in_array($vidID,$banned));
echo "<!--".$vidID."-->";
echo "<!--".$rand." : ".$perrand."-->";
$vidurl = getBestVideoFromPhotoID($vidID);
$headers = get_headers($vidurl);
foreach ($headers as $h) {
	if (substr($h,0,10)=="Location: ") {
		$vidurl = trim(substr($h,10));
		break;
	}
}

/*if (strpos($vidurl,'.mp4') !== false) {
	echo "<!--NO MP4 in url??-->"
}*/

echo "<video id='vid' onended='location.reload()' width='100%' height='100%' src='".$vidurl."' autoplay>";
?>
