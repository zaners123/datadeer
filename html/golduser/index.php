<?php require "/var/www/php/header.php" ?>
	<title>Subscription Info</title>
<?php require "/var/www/php/bodyTop.php" ?>
<?php require "/var/www/php/requireSubscription.php";?>
<h1>Hello Subscriber!</h1>
<h2>&#129420;Thank you so much for becoming a DataDeer.net subscriber! For big companies, one subscription is just another number, but for DataDeer, your subscription personally helps me, Zane, the owner of DataDeer.</h2>

<a href="/share">&#x1F4C1;Share Files (increased file limit)</a><br>

&#x1F308;Change chat color: #000<br>

&#x1F3AE;Download subscriber-only games:<br>
<a href="underwater.php">Underwater Game</a><br>
<a href="mazegame.php">Maze</a><br>
<a href="armybase.php">Army Base</a><br>
<a href="solitare.php">Solitaire</a><br>
<br>

<b>For the next features</b>, you can contact me at <a href="https://datadeer.net/chats/pchat/chatroom.php?user=deer">DataDeer chat</a> or by email at <a href="mailto:admin@datadeer.net">admin@datadeer.net</a>
<br><br>

&#129412;If you choose to be on the about page, contact me and I will add you (Response time within a day usually).
<br><br>

&#9999;If you want to use your subscriber benefit of a hand-drawn deer (YCH, funny, request, whatever), contact me.
<br><br>

&bigstar;If you want to change the hit counter, contact me with your number idea.<br>
It can be something simple like add a million, or something more advanced like increment by fibonacci sequence, primes, only even numbers, etc.<br>
I'll leave up the changed counter for a couple of days (likely 2-4 days). You can use this power once per calendar month.<br><br><br>

<div style="border: 1px solid black">
	<h4>
		Plan Info:<br>
		<?php
			printPlanInfo();
		?><br>
		<a href="/gold/cancel.php">Cancel Here</a>
	</h4>
</div>