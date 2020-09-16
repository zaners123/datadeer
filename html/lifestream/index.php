<?php require_once "/var/www/php/requireSignIn.php"; ?>
<head>
<!--	Maps-->
	<link rel="stylesheet" href="leaflet.css" integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ==" crossorigin="">
	<script src="leaflet.js" integrity="sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew==" crossorigin=""></script>

	<title>MAPS</title>
</head>
<body>
<?php require_once "/var/www/php/couch.php";?>

<?php
//if ($_SESSION["username"] != "deer") exit("AUTH");//testing security
?>

<div id="mapid" style="height:80%;"> test </div>
<script>
    var map = L.map('mapid').setView([48, -110], 4);

    //cause mapbox looks slightly prettier
    L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
        attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
        maxZoom: 20,
        id: 'mapbox/streets-v11',
        tileSize: 512,
        zoomOffset: -1,
        accessToken: 'pk.eyJ1IjoiemFuZXJzMTIzIiwiYSI6ImNrZHhqNW1rNDE2YzMycW5veW1ieHYwbTUifQ.wdqd_AuApwJ3bZCaBq-FLQ'
    }).addTo(map);

    /*L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors',
        maxZoom: 18
    }).addTo(mymap);*/

</script>
<script>
    var markers = [];
    let marker;
<?php
$db = getDatabase("tracker")["rows"];
foreach ($db as $user) {
    //get each user's list of uploaded files
    $info = sanitiseDoc(json_decode(getDocUnsafe("tracker",$user["id"]), true));
    $profile = sanitiseDoc(json_decode(getDocUnsafe("profile",$user["id"]), true));
    $username = $user["id"];
//	    echo "</script><pre>";var_dump($user);var_dump($profile);continue;
    if (!isset($info) || !isset($info["info"]) || !isset($info["info"]["Location"])) continue;
    $loc = $info["info"]["Location"];
    if (!isset($loc["last-recieved"]) || !is_numeric($loc["last-recieved"])) continue;
    $loc = $loc[$loc["last-recieved"]];
    $long = $loc["longitude"];
    $lat = $loc["latitude"];
//	$infopopup = $user["id"]."<br><a href='?info=".$user["id"]."'>info</a>";
    if (!empty($profile["icon"])) {?>
    marker = L.marker([<?=$lat?>, <?=$long?>], {
        icon: L.icon({
            iconUrl: "<?=$profile["icon"];?>",
            iconSize:     [50, 50], // size of the icon
            iconAnchor:   [25, 25], // point of the icon which will correspond to marker's location
            popupAnchor:  [0, 0] // point from which the popup should open relative to the iconAnchor
        }),
        title:'<?=$username?>'
    }).addTo(map);
    <?php } else { ?>
    marker = L.marker([<?=$lat?>,<?=$long?>],{title:'<?=$username?>'}).addTo(map);
    <?php } ?>
    marker.on('click',function(e) {
        window.location = "?info="+this.options.title;
    });
    //marker.bindPopup("<?php //echo $infopopup?>//");
    markers.push(marker);
    L.polyline([]).addTo(map);
<?php } ?>
</script>
<h3><a href="appredirectyep.php">Add yourself to this map</a></h3>
<h3><a href="index.php">Update Map</a></h3>
<?php
if (isset($_REQUEST["info"])) {
	$user = $_REQUEST["info"];
	$tracker = getDoc("tracker",$user);
	$profile = getDoc("profile",$user);
	echo "<h1>".$user."</h1>";
	if (isset($profile["biography"])) {
		echo urldecode($profile["biography"]);
	}
	//todo parse $tracker["info"]["Contacts"]
	//todo parse $tracker["info"]["Phone"]

	//todo use VideoJS library to link HLS stream here from Nginx server

	if ($user==getUsername()) {
		echo '<br>This is you! Cool!';
	}
}
?>

<video id=example-video width=600 height=300 class="video-js vjs-default-skin" controls> </video>

<script src="video.js"></script>
<script src="dash.all.js"></script>
<script src="videojs-dash.min.js"></script>
<script>
    var player = videojs('example-video');
    player.src({ src: 'datadeer.net:51818/videochat/deer?username=rememberuser&creds=rememberkey', type: 'application/dash+xml'});
    player.play();
</script>

</body>
