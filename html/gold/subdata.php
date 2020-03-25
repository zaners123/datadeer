<?php
/** main A backend used to see who subscribes. Used to access perk pages.

 */

require_once 'braintree-php-3.39.0/lib/Braintree.php';

function getGateway() {
	return new Braintree_Gateway([
		'environment' => 'sandbox',
		'merchantId' => parse_ini_file("/var/www/php/pass.ini")["braintree_merchantid"],
		'publicKey'  => parse_ini_file("/var/www/php/pass.ini")["braintree_publickey"],
		'privateKey' => parse_ini_file("/var/www/php/pass.ini")["braintree_privatekey"]
	]);
}
function isSubscribed() {
	//starts session (thus reading session variables, etc)
	if (session_status() !== PHP_SESSION_ACTIVE || !isset($_SESSION["username"])) {
		return false;
	}
	if (isset($_SESSION["subscribe"])) {
		return $_SESSION["subscribe"]==="true";
	}
	$isSubscribed = generateSubscriptionInSession();
	return $isSubscribed;
}
function generateSubscriptionInSession() {
	$isSubscribed = isUserSubscribed($_SESSION["username"]);
	$_SESSION["subscribe"] = $isSubscribed?"true":"false";
	return $isSubscribed;
}
function isUserSubscribed($username) {
	$username = strtolower($username);
	//TODO remove after payment stuffs
//	if ($username==="deer") return false;
	if ($username==="deer" || $username==="danielbacklund" || $username=="xlunah") return true;
	//find a subscription plan with their user ID
	try {
		//main get the user with this ID (their BrainTree ID is their DataDeer username)
		$userInfo = getGateway()->customer()->find($username);
		//get all their cards
		$cards = $userInfo->creditCards;
		//loop through every card
		foreach ($cards as $card) {
			//loop through every subscription (each card has independent subscriptions)
			foreach ($card->subscriptions as $subscription) {
				//var_dump($subscription);
				//echo "\n\nendsub\n\n";
				//return true if this subscription on this card is active
				if ($subscription->planId === parse_ini_file("/var/www/php/pass.ini")["braintree_plan"] && $subscription->status === Braintree_Subscription::ACTIVE) {
					return true;
				}
			}
		}
	} catch (\Braintree\Exception\NotFound $notFound) {
		error_log("isUserSubscribed user is not found so they aren't subscribed");
	}
	return false;
}
function printPlanInfo() {
	$username = strtolower($_SESSION["username"]);
	//find a subscription plan with their user ID
	try {
		//main get the user with this ID (their BrainTree ID is their DataDeer username)
		$userInfo = getGateway()->customer()->find($username);
		//get all their cards
		$cards = $userInfo->creditCards;
		//loop through every card
		foreach ($cards as $card) {
			//loop through every subscription (each card has independent subscriptions)
			foreach ($card->subscriptions as $subscription) {
				//don't show cancelled subscription plans
				if ($subscription->status === Braintree_Subscription::CANCELED) continue;
				if ($subscription->planId !== parse_ini_file("/var/www/php/pass.ini")["braintree_plan"]) continue;
				switch ($subscription->status) {
					case (Braintree_Subscription::ACTIVE):
						echo "Active - currently subscribed<br>";
						break;
					case (Braintree_Subscription::EXPIRED):
						echo "Expired - Plan expired<br>";
						break;
					case (Braintree_Subscription::PAST_DUE):
						echo "Past Due - Pay to continue services<br>";
						break;
					case (Braintree_Subscription::PENDING):
						echo "Pending - Payment going through<br>";
						break;
				}
				echo "Next Billing Date: ".$subscription->nextBillingDate->format('F d Y')."<br><br>";
				echo "Billing Period Start Date (date you last paid): ".$subscription->billingPeriodStartDate->format('F d Y')."<br>";
				$payThrough = $subscription->paidThroughDate;
				if ($payThrough != null) {
					echo "Billing Period End Date (date you have already paid until): ".$payThrough->format('F d Y')."<br>";
				}
			}
		}
	} catch (\Braintree\Exception\NotFound $notFound) {
		error_log("printPlanInfo user is not found so they aren't subscribed");
	}
	return false;
}
function cancelSubscription() {
	$username = strtolower($_SESSION["username"]);
	try {
		//main get the user with this ID (their BrainTree ID is their DataDeer username)
		$userInfo = getGateway()->customer()->find($username);
		//get all their cards
		$cards = $userInfo->creditCards;
		//loop through every card
		foreach ($cards as $card) {
			//loop through every subscription (each card has independent subscriptions)
			foreach ($card->subscriptions as $subscription) {
				//var_dump($subscription);
				//echo "\n\nendsub\n\n";
				//return true if this subscription on this card is active
				if ($subscription->planId === parse_ini_file("/var/www/php/pass.ini")["braintree_plan"] && $subscription->status !== Braintree_Subscription::CANCELED) {
					//cancel subscription
					$result = getGateway()->subscription()->cancel($subscription->id);

					//main recalculate session variable
					generateSubscriptionInSession();

					//return true (was cancelled)
					return true;
				}
			}
		}
	} catch (\Braintree\Exception\NotFound $notFound) {
		error_log("isSubscribed user is not found so they aren't subscribed");
	}
	return false;
}
/**Makes a customer
 @param username - The string username of the customer
 @return string - if empty, no errors. If non-empty, it contains the error.*/
function makeCustomer($username) {
	//should make customerId
	$customerId = getGateway()->customer()->create([
		'id' => strtolower($username),
		'firstName' => strtolower($username)
	]);
	error_log(print_r($customerId,true));
	$customerExists = false;
	if (!isset($customerId->errors)) {
		$customerExists = true;
	} else {
		foreach ($customerId->errors->deepAll() as $error) {
			//	print_r($error->code);
			if ($error->code === "91609" || $error->code ==="91510") {
				$customerExists = true;
			}
		}
	}

	if ($customerExists) {
		//echo "(note: customer exists)";
	} else if (!($customerId->success)) {
		//catch all other errors
		//echo "COULDNT MAKE CUSTOMER";
		error_log("Validation errors: \n".implode(",",$customerId->errors->deepAll()));
		return "error code A. Email admin@datadeer.net";
	}
	return "";
}

// make clientToken
/*$clientToken = $gateway->clientToken()->generate([
	"customerId" => strtolower($_SESSION["username"])
]);
echo "Client Token".json_encode($clientToken);*/

//code for one time transaction
/*$result = $gateway->transaction()->sale([
	'amount' => '100.00',
	'paymentMethodNonce' => $_POST["paymentMethodNonce"],
	'options' => [ 'submitForSettlement' => true ]
]);*/