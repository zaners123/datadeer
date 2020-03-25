<?php require "/var/www/php/header.php"; ?>
	<title>Maze Game</title>
<?php require "/var/www/php/bodyTop.php"; ?>
	<h1>An Old Maze Game</h1>
<p>
	In 8th grade, I made a maze game where you navigate a maze to the end.
	<br>
	If you want to play it you need the <a href="d.php?q=mazeGen.jar">Generator</a> and the <a href="d.php?q=mazeEscape.jar">Maze Player</a>.
	<br>
	I left it the way it was originally (apart from how now it has not hard-coded maze names so its usable...) so don't expect it to be great; it was made by an 8th grader.
</p>
Here are the directions to it:
<ul>
	<li>Install Java (it is likely already installed)</li>
	<li>Run mazeGen.jar (try double-clicking it, if that doesn't work run "java -jar mazeGen.jar" on it or look up how to open JAR files)</li>
	<li>Click "Create Maze". It'll take about 2-5 minutes to generate. Be patient.</li>
	<li>Click "Download Maze as Image" once it finishes.</li>
	<li>Run mazeEscape.jar (same way as you ran the generator jar)</li>
	<li>Select the maze file (it is in your home folder and a PNG with a long name)</li>
	<li>Play the maze (WASD for controls, follow the green arrow)</li>
</ul>
<?php require "/var/www/php/footer.php"; ?>