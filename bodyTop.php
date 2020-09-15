</head>
<body>

<audio src="/themesong.mp3" autoplay="autoplay">

<nav class="menu">
	<ul class="clearfix">
		<li><a href="/">&#129420;</a></li>
<!--		<li><a href="/"><img width="40%" src="/prateek.jpg"></a></li>-->
		<li><a href="/">Home</a></li>
		<?php
		if (isset($_SESSION) && isset($_SESSION["username"])) {
			require_once "/var/www/php/subdata.php";
			if (isSubscribed()) {
		?>
		<li>
			<a href="/golduser">Subscriber <span class="arrow">▼</span></a>
			<ul class="sub-menu">
				<li><a href="/golduser">Subscription Menu</a></li>
				<li><a href="/share">Share Files</a></li>
			</ul>
		</li>
		<?php }} ?>
		<li class="onlyOnBig">
			<a href="/chats/pchat">New <span class="arrow">▼</span></a>
			<ul class="sub-menu">
				<li><a href="/dognet">Dog Net</a></li>
				<li><a href="/game/minesweeper">Minesweeper</a></li>
				<li><a href="/cp/">Cyber Patriots</a></li>
			</ul>
		</li>
		<!--<li class="onlyOnBig">
			<a href="/">Know <span class="arrow">▼</span></a>
			<ul class="sub-menu">
				<li><a href="/calculator">Calculator</a></li>
				<li><a href="/learn">Learn</a></li>
				<li><a href="/search">Search</a></li>
				<li><a href="/test">Tests</a></li>
			</ul>
		</li>
		<li class="onlyOnBig">
			<a href="/todo">Plan <span class="arrow">▼</span></a>
			<ul class="sub-menu">
				<li><a href="/finance">Finance</a></li>
				<li><a href="/todo">Todo List</a></li>
				<li><a href="/weather">Weather</a></li>
			</ul>
		</li>-->
		<li>
			<a href="/directory">Directory</a>
		</li>
		<li style="float: right;">
			<?php if (isset($_SESSION) && isset($_SESSION["username"])) {?>
				<a href="/signout.php">Sign Out</a>
			<?php } ?>
		</li>
	</ul>
</nav>