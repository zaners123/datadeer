<?php require "/var/www/php/header.php" ?>
	<title>About Me</title>
	<style>
		body{
			text-align: center;
		}
	</style>
<?php require "/var/www/php/bodyTop.php"; ?>
	<h1>Keyboard Piano Keyboard</h1>
So I made this in late Eighth grade in less than a day. To use it you need:
<ul>
	<li>A MIDI Device (Piano Keyboard, Drums, Guitar, etc)</li>
</ul>

<br>

Step one is <a href="https://www.ubuntu.com/">Install Linux on a PC</a> (unless you already have it installed). There are millions of guides of this online.

<br><br>

After you have your linux device, run "sudo apt-get install amidi" and "sudo apt-get install xdotool". This will install the tools for MIDI reading and user input, respectively.

<br><br>

Plug in your MIDI device (likely by using a USB cable).

<br><br>

Then download <a href="pianoListen.sh">pianoListen.sh</a>. This is the script to get this to all work.

<br><br>

Run "sudo amidi -l", and put its output at the start of pianoListen (replacing the "2,0,0" with your MIDI device number).

<br><br>

<h1>You can now start the Keyboard Piano Keyboard!</h1>

Run "sudo ./pianoListen.sh" in the folder you saved the script! It will map MIDI input to your keyboard, mouse, arrow keys, etc. If you want you can label your device to know what to press.