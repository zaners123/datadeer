<?php require_once "/var/www/html/21/21header.php"; ?>
<title>The Furry Dictionary</title>
<?php require "/var/www/php/bodyTop.php"; ?>
<h1>The Furry Dictionary</h1>
Exactly what it sounds like...
<div id="bodytext">

</div>
yeah thats everything haha
<script>
	let dict = [
	    ["Awoo","A commonly used phrase of excitement, mostly by people with canine fursonas"],
		["Merp","A phrase of excitement, like <a href='#awoo'>Awoo</a>, but for sergals"],
	]
	for(let d of dict) {
	    document.getElementById("bodytext").innerHTML+="<h2 id='"+d[0]+"'>"+d[0]+"</h2>"+d[1];
	}
</script>