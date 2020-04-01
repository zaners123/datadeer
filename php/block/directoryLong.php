<table align="center" style="text-align: center">
	<thead>
	<tr>
<!--		<th>Subscriber</th>-->
		<th>&#x1F4AD;Social</th>
		<th>&#x1F4D6;Know</th>
		<th>Game</th>
		<th>About</th>
		<th>&#x1F527;Services</th>
	</tr>
	</thead>
	<tbody>
	<tr>
<!--		<td><a href="/golduser">Subscription Menu</a></td>-->
		<td><a href="/chats/pchat">Private Chat</a></td>
		<td><a href="/directory">~Directory~</a></td>
		<td><a href="/game/v2">Board Games</a></td>
		<td><a href="/other/about.php">About the Server</a></td>
		<td><a href="/deercoin">DeerCoin</a></td>

	</tr>
	<tr>
<!--		<td><a href="/share">Share Files</a></td>-->
		<td><a href="/chats/gchat">Group Chat</a></td>
		<td><a href="/learn">Learn</a></td>
		<td><a href="/game/libs">MadLibs&#8482;</a></td>
		<td><a href="/freelance">About the Owner</a></td>
		<td><a href="/other/settings.php">Settings</a></td>
	</tr>
	<tr>
<!--		<td> </td>-->
		<td><a href="/share">File Share</a></td>
		<td><a href="/search">Search</a></td>
		<td>
			<details><summary>Generators</summary>
				<a href="/game/generator/baby.php">Baby Names</a><br>
				<a href="/game/generator/fortune.php">Fortunes</a><br>
				<a href="/game/generator/insult.php">Insults</a>
			</details>
		</td>
		<td><a href="/other/changelog.php">Changelog</a></td>
		<td><a href="/other/minecraft.php">Minecraft</a></td>
	</tr>
	<tr>
<!--		<td> </td>-->
		<td><a href="/merch">Merch</a></td>
		<td><a href="/test">Tests</a></td>
		<td><a href="/game/v2/#tabs-3">Minesweeper</a></td>
		<td><a href="/weather">Weather</a></td>
		<td><a href="/dognet">DogNet</a></td>
	</tr>
	<tr>
<!--		<td> </td>-->
		<td> </td>
		<td> </td>
		<td><a href="/game/v2/#tabs-4">Sudoku</a></td>
		<td><a href="https://github.com/zaners123/DataDeer">&#60;&#47;&#62; Source</a></td>
		<td><a href="/other/terraria.php">Terraria</a></td>
	</tr>
	</tbody>
</table>
<br>
<hr>
<h3 style="margin-left:32px;text-align: left" id="list"> </h3>
<script id="script">
	let list = [
	// ["/other/aboutme.php","About the Owner"],
	["/other/about.php","About the Server"],
	["/app","App"],
	// ["/apt-mirror","Apt Mirror"],
	["/game/v2","Battleship"],
	["/game/v2","Board Games"],
	["/game/generator/baby.php","Baby Names"],
	["/calculator","Calculator"],
	["/deercoin/casino.php","Casino"],
	["/deercoin","Currency"],
	["/cp","Cyber Patriots"],
	["/other/changelog.php","Changelog"],
	["/chats/gchat","Chat - Group"],
	["/chats/pchat","Chat - Private"],
	["/game/v2","Checkers"],
	["/game/v2","Chess"],
    ["/deercoin","Deercoin"],
    ["/dog","Dog - The History of the Domestic Dog"],
	["/dognet","DogNet - Give me a potential dog image!"],
	["/directory","Directory"],
	["/share","File Pile"],
	["/game/generator/fortune.php","Fortunes"],
	["/game/v2","Games!"],
	["/chats/gchat","Group Chat"],
	["/game/generator/insult.php","Insults"],
	["/learn","Learn"],
	["/game/libs","MadLibs&#8482;"],
	// ["/merch","Merch"],
	["/other/minecraft.php","Minecraft"],
	["/game/v2","Minesweeper"],
	["/chats/pchat","Private Chat"],
	["/rs/","Raspberry Sprinkler"],
	["/directory","Recursion - Definition"],
	["/other/share.php","Share"],
	// ["/chats/schat","Secure Chat"],
	["/search","Search Engine (webpages and images)"],
	["/other/settings.php","Settings"],
	["/sewer","Sewer"],
	["/share","Share"],
	["https://github.com/zaners123/DataDeer","Source Code"],
	["/golduser","Subscription Menu"],
	["/learn/tech","Tech - Learn IT"],
	["/other/terraria.php","Terraria"],
	["/test","Tests"],
	["/other/tor.php","Tor"],
	["/weather","Weather (In Kelvin, too!)"],
];
	let lastLetter = " ";
	let out = "";
	for (let item of list) {
	console.log(item[1][0]+" "+item[1]);
	if (item[1][0] > lastLetter) {
		out+="<h1>"+item[1][0]+"</h1>";
		lastLetter = item[1][0];
	}
	out+="<a style='margin-left: 32px;' href='"+item[0]+"'>"+item[1]+"</a><br>";
}
    document.getElementById("list").innerHTML = out;
    document.getElementById("script").innerHTML = "//Nothing to be seen here";
</script>