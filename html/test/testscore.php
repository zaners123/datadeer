<?php require "/var/www/php/header.php"; ?>
	<title>IQ Score</title>
<?php require "/var/www/php/bodyTop.php"; ?>
<div style="text-align: center">
<h1>Test Results</h1>
<h2>The test results are conclusive. Here is the result:</h2>

<h1 style="font-size: 64px">
<?php
if (!isset($_POST["ans"])) header("Location: /test/api.php");

$ans = json_decode($_POST["ans"]);

//calculate IQ
$iq = 0;

switch ($_POST["type"]) {
	case "iq":
		if ($ans[0]==="3") {$iq++;}//10^3-1  999
		if ($ans[1]==="3") {$iq++;}//north america countries 39
		if ($ans[2]==="4") {$iq++;}//car door handle
		if ($ans[3]==="2") {$iq++;}//opposite of down - up
		if ($ans[4]==="1") {$iq++;}//centimeters in meter 100
		if ($ans[5]==="1") {$iq++;}//brown cows
		if ($ans[6]==="4") {$iq++;}//120 = 5!
		//The president (not the white man) DOESNT CONTAIN "WHITE" CONATINS president or GOVERNMENT or leader or US
		if (preg_match("/.*trump.*|.*president.*|.*leader.*/",strtolower($ans[7]))) {$iq++;}
		if (   !   preg_match("/.*green.*/",$ans[8])) {$iq++;}//green house - answer does not contain GREEN
		if (   !   preg_match("/.*8.*|.*eight.*/",$ans[9])) {$iq++;}//Dirt - answer does NOT CONTAIN 8 or eight
		//fly - don't score $ans[10]
		if ($ans[11]==="1") {$iq++;}//new england - yes
		if ($ans[12]==="1") {$iq++;}//parachute, duh
		if ($ans[13]==="4") {$iq++;}//question number - 14 (4)
        if ($ans[14]==="1") {$iq++;}// took away 2 apples
		$iq = 40 * pow(1.1,$iq);//do advaned bell-curve IQ math
		break;
	case "extend":
	    if ($ans[0]=='1')$iq++;//dogs cute
	    if ($ans[1]=='2')$iq++;//0
	    if ($ans[2]=='2')$iq++;//100
	    if ($ans[3]=='3')$iq++;//6
	    if ($ans[4]=='2')$iq++;//98.6
        if ($ans[5]==$ans[4])$iq+=2;//ans identical
	    if ($ans[6]=='4')$iq++;//all
	    if ($ans[7]=='3')$iq++;//Speed
	    if ($ans[8]=='1')$iq++;//multiple
	    if ($ans[9]=='3')$iq++;//scattering
	    if ($ans[10]=='2')$iq++;//drawrof
        $iq = 45 * pow(1.1,$iq);
        break;
	case "spokane":
		if ($ans[0]==="1") $iq++;
		if ($ans[1]==="2") $iq++;
		if ($ans[2]==="1") $iq++;
		if ($ans[3]==="5") $iq++;
		if ($ans[4]==="1") $iq++;
		if ($ans[5]==="2") $iq++;
		if ($ans[6]==="4") $iq++;
		if ($ans[7]==="4") $iq++;
		if ($ans[8]==="3") $iq++;
		if ($ans[9]==="1" || $ans[9]==="3") {$iq++;}//12 or 5
		if ($ans[10]==="4") {$iq++;}
		if ($ans[11]==="2") {$iq++;}
		if ($ans[12]==="1") {$iq++;}
		if ($ans[13]==="4") {$iq++;}
		if ($ans[14]==="1"||$ans[14]==="2"||$ans[14]==="3") {$iq++;}//US 195, US 395
		if ($ans[15]==="5") $iq++;
		if ($ans[16]==="2") $iq++;//7
		if ($ans[17]==="3") $iq++;//1911
		if ($ans[18]==="3") $iq++;//Shady Business Deals
		if ($ans[19]==="1") $iq++;//A railyard
		if ($ans[20]==="4") $iq++;//Executions
		if ($ans[21]==="1") $iq++;//The Design
		if ($ans[22]==="3") $iq++;//David Condon
		$iq = 25 * pow(1.1,$iq);
		break;
	case "comp":
		if ($ans[0]==="3") {$iq++;}
		if ($ans[1]==="2") {$iq++;}
		if ($ans[2]==="3") {$iq++;}
		if ($ans[3]==="4") {$iq++;}
		if ($ans[4]==="3") {$iq++;}
		if ($ans[5]==="4") {$iq++;}
		if ($ans[6]==="4") {$iq++;}
		if ($ans[7]==="1") {$iq++;}
		if ($ans[8]==="2" || $ans[8]==="4") {$iq++;}
		if ($ans[9]==="3") {$iq++;}
		if ($ans[10]==="2") {$iq++;}
		if ($ans[11]==="3") {$iq++;}
		if ($ans[12]==="1") {$iq++;}
		if ($ans[13]==="2") {$iq++;}
		if ($ans[14]==="2") {$iq++;}
		if ($ans[15]==="4") {$iq++;}
		$iq = 40 * pow(1.1,$iq);
		break;
}
echo "Your IQ is ".(substr("".$iq,0,6));
?>
</h1>

This is highly accurate according to every psychologist I asked about this IQ test.
<br><br>
If you feel your results are too low, you could try:
<ul>
	<li>Going to school</li>
	<li>Teaching yourself things</li>
	<li>Paying Attention</li>
</ul>
</div>
<?php require "/var/www/php/footer.php"; ?>