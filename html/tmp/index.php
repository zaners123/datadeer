<?php require "/var/www/php/header.php" ?>
	<title>Learn HTML</title>
<?php require "/var/www/php/bodyTop.php"; ?>

<style>
	body {
		background: #ebe url("/datadeer.png") repeat-x center;
	}
	h1 {
		color: #803;
	}
	hr {
		margin-top: 320px;
	}
	.box {
		border: #000 1px dot-dot-dash;
	}
</style>
<header>
	<hgroup>
		<h1>We got some html</h1>
		<h2>That is cool</h2>
	</hgroup>
</header>

<a href="#end">Bookmark to end of page (ID end)</a>

<div class="box">GET info:<?php echo json_encode($_GET);?></div>

<summary>
	A page about html
</summary>

<strong>1 + 1 is </strong><output>2</output>

<figure>
	<figcaption>Horse.ogg</figcaption>
	<audio controls>
		<source src="horse.ogg" type="audio/ogg">
	</audio>
</figure>

<figure>
	<figcaption>Winter Forest.mp4</figcaption>
	<video width="50%" height="50%" controls>
		<source src="winter forest.mp4" type="video/mp4">
	</video>
</figure>
<details>
	<summary>Got it on some website</summary>
	<p>I dunno which lol</p>
	<details>A<summary>A</summary></details>
	<details>B</details>
</details>

<datalist id="beans">
	<option value="Green Bean">
	<option value="Black Bean">
	<option value="Brown Bean">
	<option value="Spicy Bean">
	<option value="Chilli Bean">
</datalist>

<form method="get">
	<label>
		A useless checkbox
		<input type="checkbox">
	</label>
	<br>
	<label>
		Will recommend beans:
		<input type="text" list="beans">
	</label>

	<fieldset>
		<legend>Useful Fields</legend>
		<input type="text" placeholder="Name">
		<br>
		<label>
			TextArea:<br>
			<textarea rows="5" cols="10" placeholder="Describe your previous life:"></textarea>
		</label>
	</fieldset>
	<input type="submit" value="SUBMIT LABEL">
</form>
<hr>
<div id="end">My ID is end</div>
<footer>
	FOOTer<br>Text<br>text<br>TEXT<br style="margin-bottom: 160px;">Low Text
</footer>