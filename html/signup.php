<?php require "../php/headerNoSignin.php"; ?>
	<title>Make An Account - Data Deer</title>
	<script src='https://www.google.com/recaptcha/api.js'></script>
</head>
<body style="text-align: center;">
<h1>Make An Account</h1>
<h2 style="color: #F00;">
    <?php require "../php/signupbackend.php";?>
</h2>
<form id="signupForm" name="signup" action="/signup.php" method="post" autocomplete="off">
	<div>
		Choose a username for yourself. Choose wisely, as it can't be changed later.<br>
		<input autocomplete="off" placeholder="Username" name="username" id="username" type="text" title="username" required="required">
		<br>
		(Username 4-30 characters)
		<br><br>
		<input placeholder="Password" name="password" id="password" type="password" title="password" required="required">
		<br>
		(Password 4-7000 characters)
		<br><br>
		<input placeholder="Password (Verify)" name="passwordVerify" id="passwordVerify" type="password" title="password verify" required="required">
		<br><br>
		<div style="display: inline-block;" class="g-recaptcha" data-sitekey="6Lez4HwUAAAAAO8HAwT5e75i3gkrWVlEmqMWOmxN"></div>
		<br>
		<h3>
			By making an account, you agree to Data Deer's <a href="tos/">Terms of Service</a>
			<br>
			Also, this site <a href="other/whyCookie.php">uses a cookie</a> to remember that you are signed in.</h3>
		<br>
		<input type="submit" value="Sign Up">
	</div>
</form>
<?php require "../php/footer.php" ?>