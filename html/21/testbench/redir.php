<?php

function downloadUrl($url) {
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5_HOSTNAME);
	curl_setopt($curl, CURLOPT_HTTPPROXYTUNNEL, 1);
	curl_setopt($curl, CURLOPT_PROXY, "127.0.0.1:9050"); // Default privoxy port

	//curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
	$site = curl_exec($curl);
	curl_close($curl);
	return $site;
}

$kingurl = $_REQUEST["url"];
//var_dump($_REQUEST);
$kinginfo = parse_url($kingurl);
if (!$kinginfo) exit("BAD URL OR NO URL");

$site = downloadUrl($kingurl);

if (isset($_REQUEST["download"])) {
	header('Content-Disposition: attachment; filename="'.$displayname.'"');
	header("Content-Type: ".mime_content_type($filepath));
	echo $site;
	exit;
}

//find every href='url' or src='url' and just print the url

preg_match_all("/(?:src|href)=(?:['\"]([^'\"]+)['\"])/",$site,$matches);
$matches = $matches[1];

echo "<a href='?url=$kingurl&download=download'>Download?</a>";

foreach ($matches as &$m) {
	$info = parse_url($m);
	if (!$info) return;
	if (!isset($info["host"])) $m=$kinginfo["host"]."/".$m;
	if (!isset($info["scheme"])) $m=$kinginfo["scheme"]."://".$m;
}
echo"<pre>";
foreach ($matches as $m) {
	echo "<a href='?url=".urlencode($m)."'>".$m."</a><br>";
}