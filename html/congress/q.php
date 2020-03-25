<head>
	<title>Computer Diagnose</title>
	<link rel="stylesheet" type="text/css" href="css.css">
</head>

<body class="back quiz">

<?php

//this is used to represent the current question

/**

 @param $_GET["id"] - the current question ID

 */

class Question {
	public $text;
	public $answers;
	function __construct($text, $answers=[]) {
		$this->text = $text;
		//yes or no question
		if (sizeof($answers) > 0 && is_int($answers[0])) {
			$newans = [];
			$newans[0][0]="Yes";
			$newans[1][0]="No";
			$newans[1][1]=$answers[1];
			$newans[0][1]=$answers[0];
			$answers = $newans;
		}
		$this->answers = $answers;
	}
}

//main generate questions

//this represents all of the questions, as an associative array. TODO convert to JSON (with regex replace) for potential platform agnostic expansion
$q = [];
$q[0] = ["What type of problem is it?",[["Computer won't start",1],["Internet Problem",2],["Printer Problem",3],["Windows gives startup error",4],["Computer is very slow",5]]];
$q[1] = ["Is your computer plugged in to a working outlet and is the power supply on?",[6,7]];
$q[2] = ["What does your IP address start with?",[["Help",15],["169.254.x.x",20],["10.x.x.x",25],["172.x.x.x",25],["192.x.x.x",25],["Other IP",25,0]]];
$q[3] = ["Can you print from your application?",[16,17]];
$q[4] = ["Reboot. Does the error persist?", [29,-2]];
$q[5];
$q[6] = ["Do the fans spin or does it light up when you turn it on?",[8,9]];
$q[7] = "Plug in your computer to a working outlet, and flip the switch on the power supply to the \"-\", if it has a switch";
$q[8] = ["Does the computer beep more than once when it starts up?",[10,11]];
$q[9] = "Buy a new power supply (or ask a repair shop to inspect cabling or dust]";
$q[10] = "The beeping represents a \"POST Code\" and can be any type of hardware error, such as missing parts. You may need to take it to a technician. If you want to fix it yourself, look up the POST codes for your motherboard.";
$q[11] = ["Is your monitor on and SECURELY plugged in to the computer?",[12,13]];
$q[12] = ["If you have a video card, is your monitor plugged into your video card?",[-1,14]];
$q[13] = "Plug monitor into your computer. If the cable has screws, make sure they are screwed in. Also, power on your monitor by plugging it in then pressing the power button.";
$q[14] = "Plug your monitor into your video card. This is because \"onboard graphics\" are disabled when you have a video card.";
$q[15] = "(Press back once you get your IP address] An IP address is like a computer phone number, and computers need one to connect to the internet. On windows get your IP address by searching and running \"CMD\", then after a black window appears, type \"ipconfig\" and then enter. It will give you a line \"IP Address\" and on that will be your IP address. On Linux, search and run \"terminal\", then after the window appears, type \"ifconfig\". Then where it says \"inet addr:a.b.c.d\" is your IP address. If it starts with 255 or 127 it is not the one you are looking for.";
$q[16] = "Success, you can print.";
$q[17] = ["Can you print a test page using the OS?", [18,19]];
$q[18] = "The application can’t connect to the program. Troubleshoot the application and check you have the right printer selected";
$q[19] = ["Can you print a test page using controls at the printer? (Usually a button with a piece of paper]", [20,21]];
$q[20] = ["Check the printer is connected to the PC and are the correct drivers installed (Advanced]. If that doesn’t work, Can you print with a different printer and printer cable from this PC?",[24,26]];
$q[21] = ["Check power to the printer. Try turning the printer off and on again. Now can you print a test page by the controls at the printer?",[22,23]];
$q[23] = "Troubleshoot the printer. Check it has ink/toner, paper, and power. It may need repair.";
$q[22] = "Turn the printer online. If the problem persisits, restart the questions.";
$q[24] = "Try a new printer cable.";
$q[25];//networking APIPA
$q[26] = ["Do the printer and cable work with a different PC?",[27,28]];
$q[27] = "Your computer hardware could be unable to connect to the printer. Troubleshoot PC, including printer port and OS.";
$q[28] = "Multiple printer problems. Try troubleshooting the PC with a known good printer and cable";
$q[29] = ["Is there an error message, QR code, or error code?",[30,31]];
$q[30] = ["Look up the error code, and how to fix it. Has this fixed your problem?", [-2,31]];
$q[31] = ["Safely boot your operating system. (In Windows, hold F5 while booting]. Can you fix the error?",[-2,32]];
$q[32] = "Get a startup disk for Windows and boot from it. Go to the recover option, and see if it can fix your PC.";
//main get ID of input
if (!isset($_GET["id"])) {$_GET["id"]=0;}
$id = (int)$_GET["id"];
if ($id < 0) {echo "I am unsure";exit;}

//main print question
$question = $q[$id];
//var_dump($question);
printQ($question);

function printQ($question) {
	if (is_string($question)) {
		echo $question;
		return;
	}
	if (is_array($question)) {
		$text = $question[0];
		unset($question[0]);
		$answers = $question[1];
	} else {
		$text = $question->text;
		$answers = $question->answers;
	}

	//print question
	echo $text;
	echo "<ul>";
	foreach ($answers as $k=>$a) {
		echo "<li class='answers'><a href=q.php?id=".$a[1].">".$a[0]."</a></li>";
	}
	echo "</ul>";

}

?>
</body>
