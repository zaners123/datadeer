<?php require "/var/www/php/headerNoSignin.php" ?>
	<title>Minecraft</title>
<?php require "/var/www/php/bodyTop.php"; ?>
<div class="c">
	<h1>MODDED MINECRAFT</h1>
	<div>It's now a modded Minecraft server! To play:</div>
	<ol>
		<li>Install <a href="https://www.twitch.tv/downloads">the twitch client	</a></li>
		<li>then go to the mods tab in the top</li>
		<li>install and run the modpack "Omnifactory", then join multiplayer like in regular minecraft.</li>
	</ol>

</div>

<div class="c">
	<h1>DataDeer.net is a minecraft server (on desktop and mobile)!</h1>
	<br>
	<h1 style="font-size: 64px" class="rainbow">STATUS</h1>
	<h1 id="people">Loading...</h1>
	<hr>
	<br><br><br>
	<h1>To join, just put DataDeer.net in the Multiplayer bar.</h1>
	<h1>If you have a phone, tap multiplayer, add server, "datadeer.net", join.</h1>
</div>
<script>
<?php //require "../../php/mineStat.php";?>

let msg = "Go Join!";

let people = Number(JSON.player_count);

if (people==null) {

} else if (people>5) {
    msg = "There are "+people+" online! It's a party! Get on!";
} else if (people>1) {
    msg = "There are currently "+people+" online! Get on and play!";
} else if (people===1) {
    msg = "There is 1 person online! Go and join them!";
} else if (people===0){
    msg = "No one is currently online...";
}
document.getElementById("people").innerText = msg;

</script>