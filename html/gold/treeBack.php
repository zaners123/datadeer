<?php
require "/var/www/php/requireSignIn.php";

require_once "/var/www/php/subdata.php";
require_once 'braintree-php-3.39.0/lib/Braintree.php';

/*main list of keys/tokens you have or can generate

merchantId, publicKey, and privateKey are on the api keys page
your tokenization key (public) is on the api keys page

customerId you have to make a customer

clientToken can be gotten from "a customer id"
paymentMethodNonce is gotten by single-payments

paymentMethod from customerId and paymentMethodNonce

todo Get that Apple Pay paperwork done so people can use it as payment

*/

//main Instantiate a Braintree Gateway like this:

//echo isSubscribed($_SESSION["username"])?"SUBBED":"NO SUBBED";

$cust = makeCustomer($_SESSION["username"]);
if (!empty($cust)) {
	echo $cust;
	exit;
}
//should make payment method?
$paymentMethod = getGateway()->paymentMethod()->create([
	'customerId' => strtolower($_SESSION["username"]),
	'paymentMethodNonce' => $_POST["paymentMethodNonce"]
]);
if ($paymentMethod->success) {
//	echo "made payment method: " . json_encode($paymentMethod);
} else {
	echo "error code B. Email admin@datadeer.net";
	error_log("FAILED AT MAKING PAYMENT METHOD ".json_encode($paymentMethod));
}


//error_log(json_encode($paymentMethod));
//var_dump($paymentMethod);
//code for subscription
$subscriptionResult = getGateway()->subscription()->create([
	'paymentMethodToken' => $paymentMethod->paymentMethod->token,
	'planId' => parse_ini_file("/var/www/php/pass.ini")["braintree_plan"]
	//you can't set the ID to their username because if they (sub,un,sub) then it will use the same ID twice
	//	CAN'T xx> 'id' => strtolower($_SESSION["username"])
]);

if ($subscriptionResult->success) {
	//print_r("success!: " . $subscriptionResult->transaction->id);
	echo "<br>Success! You are now an Official DataDeer Subscriber!";
	generateSubscriptionInSession();
} else if ($subscriptionResult->transaction) {
//	print_r("Error processing transaction:");
	echo "Error code C. Email admin@datadeer.net";
	error_log("pay err code: " . $subscriptionResult->transaction->processorResponseCode);
//	print_r("\n  text: " . $subscriptionResult->transaction->processorResponseText);
} else {
	echo "Error code D. Email admin@datadeer.net";
//	print_r("Validation errors: \n");
	error_log("Pay error:".var_export($subscriptionResult->errors->deepAll(),true));
}