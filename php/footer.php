<footer class="foot c"><span class="white"><?php
$bottomText = [
	"Make sure you watch out for datadeer on the road",
	"Wow, I need a graphic designer...",
	"Making a website is hard",
	"Now with <span style='color: #ff22ff'>pink</span> backgrounds!",
	"Now with 14 CD drives",
	"Would you like to play a game?",
	"DataDeer 2.0 - Now with 1.44\" floppy disks",
	"Bug Fixes: Removed Herobrine",
	"21 thousand employees",
	"Mouse Compatible!",
	"Open Source!",
	"Now with long passwords!",
	"Now with more pumpkin spice",
	"Now on Netscape Navigator 9.0.0.6",
	"<a class='white' href='/dog'>Now with so many dogs</a>",
	"Stop being reasonable, this is the Internet!",
	"There's no place like ".$_SERVER['REMOTE_ADDR'],
	"A bit of beauty",
];
$bottomText = ["Now with two deers"];
echo $bottomText[array_rand($bottomText, 1)];
?></span></footer>
</body>
</html>