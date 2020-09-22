</head>
<body>
<?php
if (isset($doc["music"]) && $doc["music"] == "true") {
	function glob_recursive($pattern, $flags = 0){
		$files = glob($pattern, $flags);
		foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir)
			$files = array_merge($files, glob_recursive($dir.'/'.basename($pattern), $flags));
		return $files;
	}
	$songs = glob_recursive("themesongs/*.mp3");
    echo '<!--Go to URL: http://freemusicarchive.org/ if you wanna know where these songs are from. MP3 info has more info, such as copyright, on current song-->';
	echo '<audio src="'.$songs[array_rand($songs)].'" autoplay="autoplay" loop="loop"> </audio>';
}
?>
<nav class="menu">
	<ul class="clearfix">
		<li><a href="/">&#129420;</a></li>
		<li><a href="/">Home</a></li>
		<li class="onlyOnBig">
			<a href="/chats/pchat">New <span class="arrow">▼</span></a>
			<ul class="sub-menu">
				<li><a href="/lifestream">LifeStream!</a></li>
				<li><a href="/app">The App</a></li>
				<li><a href="/dognet">Dog Net</a></li>
				<li><a href="/cp/">Cyber Patriots</a></li>
			</ul>
		</li>
		<li>
			<a href="/directory">Directory</a>
		</li>
		<li style="float: right;">
			<?php if (isset($_SESSION) && isset($_SESSION["username"])) {?>
				<a href="/signout.php">Sign Out</a>
			<?php } ?>
		</li>
		<li style="float: right;">
			<?php if (isset($_SESSION) && isset($_SESSION["username"])) {?>
				<a href="/other/settings.php">Settings</a>
			<?php } ?>
		</li>
	</ul>
</nav>