<script>
	function check() {
	    if (
	        document.getElementsByName("username").value === "admin" &&
	        document.getElementsByName("password").value === "mySecureAdminPassword"
	    ) {
	        document.getElementById("out").innerHTML="<h1 style='color: #0f0'>ACCESS GRANTED</h1>"
	    } else {
            document.getElementById("out").innerHTML="<h1 style='color: #f00'>ACCESS DENIED</h1>"
	    }
	    return false;
	}
</script>
<h1>Welcome to the CTF! But first, sign in.</h1>
<h2>In case not obvious, you gotta "hack" your way in. Actual hacking (Ex: destroying site functionality) is not allowed.</h2>
<div id="out"></div>
<form onsubmit="return check()" method="post">
	<input type="text" name="username" placeholder="Username"><br>
	<input type="text" name="password" placeholder="Password"><br>
	<input type="submit" value="Sign In">
</form>

