<?php

function getLicenceList() {
	return queryAPI(array(
		'method'    =>  'flickr.photos.licenses.getInfo',
	));
}

function downloadGET($url) {
	$curl = curl_init();
	//dont directly pass URL unless you trust the server
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
	//dont do this unless you trust the server (could make u DDOSSED)
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	$ret = curl_exec($curl);
	curl_close($curl);
	return $ret;
}

function getBestVideoFromPhotoID($photoID) {
	$sizes = getSizes($photoID);
//	var_dump($sizes);
	//for each photo, get the best MP4 link
	foreach ($sizes["sizes"]["size"] as $size) {
		if ($size["media"]=="video" && $size["label"]=="HD MP4") {
			return $size["source"];
		}
	}
	//or just the OK video link
	foreach ($sizes["sizes"]["size"] as $size) {
		if ($size["media"]=="video" && $size["label"]=="Site MP4") {
			return $size["source"];
		}
	}
	return "";
}

function getPhotoLocalURL($photoID) {
	return "/var/www/livestreamContents/".preg_replace("/\D/","",$photoID).".mp4";
}

function downloadSearchResults($searchResult) {
	foreach ($searchResult as $photo) {
		$photoID = $photo["id"];
		echo $photoID.":";
		$saveloc = getPhotoLocalURL($photoID);
		if (!file_exists($saveloc)) {
			$source = getBestVideoFromPhotoID($photoID);
			$bits = file_put_contents($saveloc, file_get_contents($source));
			echo " Writing to '".$saveloc."'";
			if ($bits === false) {
				echo "AWWW I FAILED U";
			} else {
				echo " Wrote ".$bits." bits";
			}
		} else {
			echo "Already got";
		}
		echo "<br><br>";
	}
}

function giveUserFile($contents, $filename = "vid.mp4", $mime = "video/mp4") {
	header('Content-Disposition: attachment; filename="'.$filename.'"');
	header("Content-Type: ".$mime);
}

//main calculate image search results
function queryAPI($params) {
	//main put in flickr api key
	$params["api_key"] = parse_ini_file("/var/www/php/pass.ini")["flickr"];
	$params['format']  = 'php_serial';

	//build the API URL to call
	$encoded_params = array();
	foreach ($params as $k => $v){
		$encoded_params[] = urlencode($k).'='.urlencode($v);
	}
	$url = "https://api.flickr.com/services/rest/?".implode('&', $encoded_params);
	//call the API and decode the response
	return unserialize(file_get_contents($url));
}

function getSizes($photoID) {
	return queryAPI(array(
		'method'	=> 'flickr.photos.getSizes',
		'photo_id'    => $photoID,
	));
}

function flickr_video_embed($video_url, $width="400", $height="300", $info_box="true") {
	$markup = <<<EOD
<object type="application/x-shockwave-flash" width="$width" height="$height" data="$video_url"  classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"> <param name="flashvars" value="flickr_show_info_box=$info_box"></param> <param name="movie" value="$video_url"></param><param name="bgcolor" value="#000000"></param><param name="allowFullScreen" value="true"></param><embed type="application/x-shockwave-flash" src="$video_url" bgcolor="#000000" allowfullscreen="true" flashvars="flickr_show_info_box=$info_box" height="$height" width="$width"></embed></object>
EOD;
	return $markup;
}

function searchPhotos($search, $mediaType = 'photos',$page = 1, $perPage = 100) {
	$rsp = queryAPI(array(
		'method'	=> 'flickr.photos.search',
		'text'      => $search,
		'media'     => $mediaType,
		'extras'    => 'media',
		'page'      => $page,
		'per_page'  => $perPage,
	));
	if ($rsp['stat'] !== 'ok') {
		exit("Failed API call");
	}
	return $rsp["photos"]["photo"];
}
