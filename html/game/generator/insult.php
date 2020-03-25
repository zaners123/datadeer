<?php require "/var/www/php/header.php" ?>
	<title>Insult</title>
<?php require "/var/www/php/bodyTop.php"; ?>
<div style="text-align: center">

<h1>Mean Insult:<br><br>You're a <span id="name"></span></h1><br>
<script>
    let adjective = ["dirty","sack of","creep-face","poop-smelling","smelly","gross","double","dork","foolish","dumb","foolish","super","crazy","stupid"];
    let part = ["chunk'a","chunker","barbie","nugget","lung-mold","tummy-tugger","rug-rat","dust-collector","fink","stinker","sinker",
	    "beef-crumb","donkey","carpet-sniffer","scalawag","blubber-knuckle","glue-stick","lolly-gagging","pickle-licking","dumpster-fire","clump-faced","nugget-bottom",
	    "yodeling-rock","rot-legged","skunk","milk-man","chunk","bag of ferrets","knob","nut","hedge-creeper","jerk","cow","dipstick",
	    "cretin","dunce","wanker","fopdoodle","fungus","doorbell","bottom","wrench","dodo","airhead","chicken","butt","rump","poop","doo-doo","rat","lard","face","fat","tool","rock","lump","tard"];
    function insult() {
        let n = adjective[Math.floor(Math.random()*adjective.length)];
        n+= " "+part[Math.floor(Math.random()*part.length)];
        n+= " "+part[Math.floor(Math.random()*part.length)];
        document.getElementById("name").innerHTML = n;
    }
    insult();
</script>

<form><input type=button value="Another!" onClick="insult()"></form>

</div>
</body>
</head>