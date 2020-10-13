<?php require "localHeader.php";
$info = json_decode(base64_decode($_POST["info"]),true);
printFlightInfo($info,"is for sale");

echo "<br>The flight to ".$info["flight_to"]." boards at ".$info["t1"]." and arrives at ".$info["t2"];

if (isset($info["date_return"])) {
	echo "<br>The flight back to ".$info["flight_from"]." boards at ".$info["t3"]." and arrives at ".$info["t4"];
}

?>

<form action="receipt.php" method="post" style="border: 1px #000;background: #aaa">
	<label>Card Number:<input type="text" value="1234-5678-9012-3456" disabled="disabled"></label><br>
	Price: $<?=$info["price"];?><br>
	<input type="hidden" name="info" value="<?=base64_encode(json_encode($info));?>">
	<input type="submit" value="Make Purchase"><br>
</form>