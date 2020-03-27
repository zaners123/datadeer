<?php require "/var/www/php/header.php"; ?>
	<title>Calculator</title>
<?php require "/var/www/php/bodyTop.php"; ?>
<?php exit("DISABLED");?>

<div style="text-align: center">
<h1>
Calculator&emsp;(<a href="graph.php">Switch to Graphing</a>)
<br>
<form onsubmit="return solve()" >
	<input id="problem" name="problem" type="text" placeholder="Problem">
</form>
<br>
<span id="res"></span>
</h1>
Some examples of things you can plug in are:<br>
2sin(30)^2-15<br>
fact(50)<br>
ln(15)-sqrt(e)<br>
15-abs(-5)<br>
5-(10)<br>
1/-81<br>
5E6<br>
<script>
	function solve() {
	    let problem = document.getElementById("problem").value;
        fetch("calcBackend.php?type=c&problem[]="+encodeURIComponent(problem), {credentials: "same-origin"}).then(function (response) {
            response.text().then(function (text) {
                let res = text.split(",");
                // res[1] = res[1].replace("/\.0*$/","");
                document.getElementById("res").innerHTML = res[0] + " = " + res[1];
            });
        });
        return false;
	}
    //alert(res);
</script>
</div>
<?php require "/var/www/php/footer.php"; ?>