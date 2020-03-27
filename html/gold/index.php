<?php require "/var/www/php/header.php"; ?>
<link rel="stylesheet" type="text/css" href="/css/sub.css"/>
<title>Subscribe</title>
<meta charset="utf-8">
<style>
	.btn {
		border: none;
		font-family: inherit;
		font-size: inherit;
		color: inherit;
		background: none;
		cursor: pointer;
		padding: 25px 80px;
		display: inline-block;
		margin: 15px 30px;
		text-transform: uppercase;
		letter-spacing: 1px;
		font-weight: 700;
		outline: none;
		position: relative;
		-webkit-transition: all 0.3s;
		-moz-transition: all 0.3s;
		transition: all 0.3s;
	}
	.btn-4 {
		border-radius: 50px;
		border: 3px solid #fff;
		color: #fff;
		overflow: hidden;
	}
	.btn-4:active {
		border-color: #17954c;
		color: #17954c;
	}
	.btn-4:hover {
		background: #24b662;
	}
	.btn-4:before {
		position: absolute;
		height: 100%;
		font-size: 125%;
		line-height: 3.5;
		color: #fff;
		-webkit-transition: all 0.3s;
		-moz-transition: all 0.3s;
		transition: all 0.3s;
	}
	.btn-4:active:before {
		color: #17954c;
	}
	.btn-4a:before {
		left: 130%;
		top: 0;
	}
	.btn-4a:hover:before {
		left: 80%;
	}
	.icon-arrow-right:before {
		content: "▶";
	}
</style>
<script src="https://js.braintreegateway.com/web/dropin/1.16.0/js/dropin.min.js"></script>
<?php require "/var/www/php/bodyTop.php"; ?>

<div class="c">
	<div style="margin-top: 32px" class="gold">DataDeer Gold</div>

	<?php
	require_once "/var/www/php/subdata.php";

	if (isSubscribed()) {?>
	<br>
	<div style="font-size:120%;color: #FFF;padding:64px;background-color: #005925;">
				<a style="color:#00c8ff;" href="/golduser/">You are subscribed. Go here for more info.</a>
	</div>
	<?php } else { ?>

	<h2>
		Become a DataDeer Gold member and unlock unique features such as:<br><br>
		&#x1F4C1; Store and easily share 20GB of files &#x1F4C1;<br>
		&#x1F308; <span class="rainbow">Can have any color in chat</span> &#x1F308;<br>
		&#x1F3AE; Play games such as Underwater, Maze, Solitaire, and ArmyBase &#x1F3AE;<br>
		&#129412; Can be on the DataDeer.net About page &#129412;<br>
		&#9999; The owner will personally send you a hand-drawn deer (YCH, funny, request, whatever) &#9999;<br>
		&bigstar; Can change hit counter &bigstar;<br>
		&#129420; Help the developer make more content &#129420;<br>
	</h2>

	<div style="font-size:120%;color: #FFF;padding:64px;background-color: #17954c;">
		Subscribing is only $9.99 a month<br>
		Your credit card information is given to BrainTree (a division of PayPal), not to DataDeer.
		<div id="dropin-wrapper">
			<div id="checkout-message"></div>
			<div id="dropin-container"></div>
			<button id="submit-button" class="btn btn-4 btn-4a icon-arrow-right">Submit payment</button>
		</div>
		<br>
	</div>
	<?php } ?>
	<h4>
		Cancel subscription <a href="/gold/cancel.php">Here</a><br>Terms of service <a href="/tos">Here</a><br>Email <a href="mailto:support@datadeer.net">support@datadeer.net</a> for customer service or payment help.
	</h4>
</div>

<!-- includes the Braintree JS client SDK -->
<script src="https://js.braintreegateway.com/web/dropin/1.16.0/js/dropin.min.js"></script>

<!-- includes jQuery -->
<script src="https://code.jquery.com/jquery-3.2.1.min.js" crossorigin="anonymous"></script>

<script>
    var button = document.querySelector('#submit-button');

    braintree.dropin.create({
        // Insert your tokenization key here
        authorization: parse_ini_file("/var/www/php/pass.ini")["braintree_authorization"],
        container: '#dropin-container'
    }, function (createErr, instance) {
        button.addEventListener('click', function () {
            instance.requestPaymentMethod(function (requestPaymentMethodErr, payload) {
                // When the user clicks on the 'Submit payment' button this code will send the
                // encrypted payment information in a variable called a payment method nonce
                $.ajax({
                    type: 'POST',
                    url: 'treeBack.php',
                    data: {'paymentMethodNonce': payload.nonce}
                }).done(function(result) {
                    // Tear down the Drop-in UI
                    instance.teardown(function (teardownErr) {
                        if (teardownErr) {
                            console.error('Could not tear down Drop-in UI!');
                        } else {
                            console.info('Drop-in UI has been torn down!');
                            // Remove the 'Submit payment' button
                            $('#submit-button').remove();
                        }
                    });
                    $('#checkout-message').html(result);
                    /*if (result.success) {
                        $('#checkout-message').html('<h1>Success</h1><p>Your Drop-in UI is working! Check your <a href="https://sandbox.braintreegateway.com/login">sandbox Control Panel</a> for your test transactions.</p><p>Refresh to try another transaction.</p>');
                    } else {
                        console.log(result);
                        $('#checkout-message').html('<h1>Error</h1><p>Check your console.</p>');
                    }*/
                });
            });
        });
    });
</script>
<?php require "/var/www/php/footer.php"; ?>