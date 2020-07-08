<footer class="foot c"><div class="white"><?php
$bottomTexts = [
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
	"Now with two deers!",
];

$disclaimers = [
	"Do not add toner",
	"Do not bend, fold, mutilate, or spindle",
	"Do not drive or operate heavy machinery while using this product",
	"Do not use while operating a motor vehicle or heavy equipment ",
	"Do not eat",
	"Do not put in mouth",
	"Do not turn upside down",
	"Do not prepare in a toaster oven",
	"Contents may be hot after heating",
	"Contents may be Fragile",
	"Do not use in shower",
	"Do not use if seal is broken ",
	"Do not write below this line<hr>",
	"Dog not included",
	"Harmful if swallowed",
	"May cause excitability",
	"This side up",
	"This face up",
];
$bottomText = $bottomTexts[array_rand($bottomTexts, 1)];
$disclaimer = $disclaimers[array_rand($disclaimers, 1)];
//$bottomText = $bottomTexts[14];
//$disclaimer = $disclaimers[13];
?>
<div><?=$bottomText?></div>
<div class="red"><?=$disclaimer?></div>
</footer>
</body>
</html>