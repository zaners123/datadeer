<?php
require "localHeader.php";

$info = json_decode(base64_decode($_POST["info"]),true);

?>

Here is your ticket:<br>
<img src="ticketDraw.php?info=<?=$_POST["info"];?>">

<br>

<?php if (isset($info["withHotel"]))echo "You're staying in the \"".$info["hotelName"]."\" In room 250<br>";?>
<?php if (isset($info["withCar"]))echo "You're renting a \"".$info["carName"]."\" to drive<br>";?>

<a href="ticketDraw.php?info=<?=$_POST["info"];?>">Print your ticket by clicking here</a>
<hr>
<?php
printFlightInfo($info,"<b>has been purchased</b>");
?>

