<?php require "/var/www/php/header.php" ?>
<title>Cancel Subscription</title>
<?php require "/var/www/php/bodyTop.php" ?>
<?php

require_once "/var/www/php/subdata.php";


if (isset($_POST["yes"])
	&&
	(strpos(strtolower($_POST["yes"]), 'cancel') !== false)) {
	cancelSubscription();
}?>
<h1>You are currently <?php echo isSubscribed()?"Subscribed":"Not subscribed";?></h1>
<br>
<h1>We are sorry to see you want to cancel your subscription. Remember, your subscription is what keeps DataDeer.net running</h1>
<h1>To cancel your subscription, type "cancel" in the box, then click the button.<br>
	<form action="cancel.php" method="post">
		<input type="text" name="yes" placeholder="">
		<input type="submit" value="I want to cancel my subscription">
	</form>
</h1>
<h2>If you need help, email <a href="mailto:support@datadeer.net">support@datadeer.net</a>, and I can cancel it for you.</h2>