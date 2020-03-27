<?php
require "/var/www/php/startSession.php";
exit("DISABLED"); ?>
//main default to using guest account, so people don't have to sign in
if (!isset($_SESSION["username"])) {
	$_SESSION["username"] = "guest";
}

if (isset($_POST["adduser"])) {
	if (!isset($_POST["firstname"]) || !isset($_POST["lastname"]) || !isset($_POST["grade"])) {
		echo "ERR USER INPUT";exit;
	}
	//add a user
	$conn = mysqli_connect("localhost","website",parse_ini_file("/var/www/php/pass.ini")["mysql"],"fbla");
	$query = sprintf(
		'insert into students (admin,firstname,lastname,grade) values ("%s","%s","%s",%s)',
		mysqli_real_escape_string($conn, $_SESSION["username"]),
		mysqli_real_escape_string($conn, $_POST["firstname"]),
		mysqli_real_escape_string($conn, $_POST["lastname"]),
		mysqli_real_escape_string($conn, $_POST["grade"])
	);
	mysqli_query($conn,$query);
	mysqli_close($conn);
}




require "/var/www/php/headerNoSignin.php";
?>
	<title>FBLA Community Service - <?=$_SESSION["username"]?></title>
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script>
	    $( function() {
	        $( "#accordion" ).accordion();
	    } );
	</script>
	<style>
		table.center {
			margin: 0 auto;
			text-align: center;
		}
		table > * {
			text-align: left;
		}
	</style>
</head>
<body>
	<h1>FBLA Community Service tracking tool.</h1>
	<?php if (!isset($_SESSION["username"]) || $_SESSION["username"] === "guest") {?>
		<h2 class="red">You are currently not signed in, and viewing example statistics. <a href="/">Sign in</a> so only your account can see the data.</h2>
	<?php } else {?>
		<h2 class="green">Signed in as <?=$_SESSION["username"];?></h2>
	<?php }	?>
<!--    Uses JQuery accordian to give nice folding menus-->
	<div id="accordion">
		<h2>Add a Student</h2>
		<div>
			<form method="post">
				<input type="hidden" name="adduser" value="adduser">
				<table class="center">
<!--					Required fields are so that you have to put a name-->
					<tr><td>First Name:</td><td><input name="firstname" type="text" placeholder="First Name" required></td></tr>
					<tr><td>Last Name:</td><td><input name="lastname" type="text"  placeholder="Last Name" required></td></tr>
					<tr><td>Grade:</td><td><input name="grade" type="number" min="5" max="15" placeholder="grade" required></td></tr>
					<tr><td> </td><td><input type="submit" value="Add Student"></td></tr>
				</table>
			</form>
		</div>
		<h2>View/Edit a Student</h2>
		<div>(Form to edit/remove students)</div>
		<h2>Export All Data</h2>
		<div>
<!--			TODO export script-->
			<form method="post" action="export.php">
				<table class="center">
					<tr>
						<td>Group by:</td>
						<td><label><input type="radio" name="group_by" value="student" checked>Student</label></td>
						<td><label><input type="radio" name="group_by" value="student">Event</label></td>
					</tr>

					<tr>
						<td>Export as:</td>
<!--					Each row would be something like {last,first} or {last,first,event_name,event_hours}-->
						<td><label><input type="radio" name="filetype" value="CSV" checked>CSV</label></td>
<!--						For JSON and XML just print the database-->
						<td><label><input type="radio" name="filetype" value="JSON">JSON</label></td>
						<td><label><input type="radio" name="filetype" value="XML">XML</label></td>
					</tr>
<!--					Something like <user><event>name:"Helped Food Bank",hours:"5"</event></user>-->
					<tr></tr>
					<tr></tr>
				</table>
				<input type="submit" name="export" value="export">
			</form>
		</div>
	</div>

	<p>
		This was made for FBLA by Zane Chalich during the 2019-2020 schoolyear. I started on December 4th, 2019.
	</p>
	<p>
		It was based off of the <a href="https://www.fbla-pbl.org/competitive-event/coding-programming/">Official Guidelines</a>
	</p>
</body>