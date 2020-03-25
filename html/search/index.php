<?php
session_start();
require "/var/www/php/flickr.php";
require "/var/www/php/headerNoSignin.php"; ?>
<style>
    .headerall {
	    top:15%;
	    position: fixed;
	    width: 100%;
	    color: black;
    }
    .bar {
	    text-align: center;
    }
    .tab {
        text-indent: 5%;
    }
    li {
        text-indent: 0;
    }
</style>
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<?php

require "/var/www/php/bodyTop.php";

$pageSearch = isset($_GET["q"])?preg_replace("/[\W]/","",$_GET["q"]):"";
$imgSearch = isset($_GET["i"])?$_GET["i"]:"";
if (!empty($pageSearch)) {
//main give them their page search results
?>
<img alt="DataDeer Search" width="15%" src="DataSearch.png"/>
<div class="headerall bar">
	<form style="display: inline;" id="searchForm" name="searchForm" method="GET" action="index.php">
		<input type="text" name="q" id="q" autofocus="true" size="50" placeholder="Search by keyword"/>
		<input type="submit" value="Search"/>
	</form>
</div>
<div class="searchResult" id="searchResult">
	<div class="tab"><br><br><br>
		<?php
		//search in SQL
		$conn = mysqli_connect("localhost","website",parse_ini_file("/var/www/php/pass.ini")["mysql"],"search");
		$query = sprintf(
			'select urlid from keyword where keyword="%s" limit 50',
			mysqli_real_escape_string($conn, $pageSearch)
		);
		$res = mysqli_query($conn,$query);



		//after all sql, start session and go
		if (mysqli_num_rows($res) == 0) {
			echo "No results found for search \"".$pageSearch."\"";
		} else {
			echo "<ol id='resultsOL'>";
			while ($urlIDs = mysqli_fetch_assoc($res)) {

				$query = sprintf(
					'select * from url where urlid="%s"',
					mysqli_real_escape_string($conn, $urlIDs["urlid"])
				);

				$page = mysqli_fetch_assoc(mysqli_query($conn,$query));
				echo "<li><a href=\"".$page["url"]."\">".$page["title"]."</a><br><div class=\"tab\">".$page["description"]."</div></li>";
			}
			echo "</ol>";
		}
		mysqli_close($conn);?>
		<script>
	    document.onkeypress = function (e) {
	        let get = window.event?event:e;
	        let key = get.keyCode?get.keyCode:get.charCode; //get character code
	        if (e.ctrlKey && 48<=key && key<=57)
	            window.location = document.getElementById('resultsOL').children[key-49].children[0].getAttribute("href");
	    };</script>
	</div>
</div>
<?php
return;

} else if (!empty($imgSearch)) {
//main imgSearch bar
?>
<img alt="DataDeer Search" width="15%" src="DataSearch.png"/>
<div class="headerall bar">
	<form style="display: inline;" id="searchForm" name="searchForm" method="GET" action="index.php">
		<input type="text" name="i" id="i" autofocus="true" size="50" placeholder="Image Search"/>
		<input type="submit" value="Search"/>
	</form>
</div>
<div class="searchResult" id="searchResult">
	<div class="tab"><br><br><br>
<?php

$rsp = searchPhotos($imgSearch);
if ($rsp['stat'] == 'ok'){
	foreach ($rsp["photos"]["photo"] as $imgData) {
		if ($imgData["media"]=="video") {
			echo "<video src='https://farm".$imgData["farm"].".staticflickr.com/".$imgData["server"]."/".$imgData["id"]."_".$imgData["secret"].".mp4'>";
			var_dump($imgData);
			exit;
		} else {
			echo "<img src='https://farm".$imgData["farm"].".staticflickr.com/".$imgData["server"]."/".$imgData["id"]."_".$imgData["secret"].".jpg'>";
		}
	}
} else {
	echo "Call failed!";
}
echo "This product uses the Flickr API but is not endorsed or certified by SmugMug, Inc.";
return;

} else {
//main show the search bars, there is nothing put in
?>
<div style="text-align: center">
	<br>
	<img alt="DataDeer Search" width="25%" src="DataSearch.png"/>
	<br>
	<form id="searchForm" name="searchForm" method="GET" action="index.php">
		<input type="text" name="q" id="q" autofocus="true" size="50" placeholder="Page Search"/>
		<input type="submit" value="Page Search"/>
	</form>
	<br>
	<form style="display: inline;" id="searchForm" name="searchForm" method="GET" action="index.php">
		<input type="text" name="i" id="i" autofocus="true" size="50" placeholder="Image Search"/>
		<input type="submit" value="Image Search"/>
	</form>
</div>
<br><br><br><br><br><br><br><br><br>
This product uses the Flickr API but is not endorsed or certified by SmugMug, Inc.
<?php
}
?>