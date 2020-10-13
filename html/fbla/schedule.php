<?php
require "localHeader.php";

$info = $_POST;
calcFlight($info);
?>

<div>
	<?php
	for ($i=0;$i<10;$i++) {
		$offset = produceOffset($info);
		printFlightInfo($offset, "was found");
		?>
		<form method="post" action="buy.php">
			<input type="hidden" name="info" value="<?=base64_encode(json_encode($offset));?>">
			<input type="submit" value="Purchase">
		</form>
		<br>
	<?php } ?>
</div>