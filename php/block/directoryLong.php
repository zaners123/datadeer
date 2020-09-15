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
	    ["/lifestream","Lifestream"],
	    ["/game/libs","MadLibs&#8482;"],
		["/lifestream","Map"],
		// ["/merch","Merch"],
		["/other/minecraft.php","Minecraft"],
		["/game/v2","Minesweeper"],
		["/chats/pchat","Private Chat"],
		["/rs/","Raspberry Sprinkler"],
		// ["/directory","Recursion - Definition"],
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
		["/21","Twenty One (No, not the band...)"],
		["/weather","Weather (In Kelvin, too!)"],
	];
	let lastLetter = " ";
	let out = "";
	for (let item of list) {
	// console.log(item[1][0]+" "+item[1]);
	if (item[1][0] > lastLetter) {
		out+="<h1>"+item[1][0]+"</h1>";
		lastLetter = item[1][0];
	}
	out+="<a style='margin-left: 32px;' href='"+item[0]+"'>"+item[1]+"</a><br>";
}
    document.getElementById("list").innerHTML = out;
    document.getElementById("script").innerHTML = "//Nothing to be seen here";
</script>