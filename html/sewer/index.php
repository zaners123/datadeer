<?php require "/var/www/php/headerNoSignin.php";?>
<title>Sewer Calculator</title>
<?php require "/var/www/php/bodyTop.php";?>
<h1>Sewer Calculator</h1>
<hr>
<form onsubmit="return calculate()">
	<h3>Hours Ran</h3>
	<input id="inHoursRan" step="any" class="field" type="number" placeholder="Hours ran" required>
	<h3>Rate at which lubricant is needed</h3>
	<input id="inHourRate" step="any" class="field" type="number" placeholder="Hour rate" required>/<input id="inOunceRate" step="any" class="field" type="number" placeholder="Ounce rate" required><br><br>
	<h3>Ounces per stroke</h3>
	<input id="inStroke" step="any" class="field" type="number" placeholder="Ounces per stroke" value=".025"><br>

	<input class="field" step="any" type="submit" value="Calculate">
</form>

<h1 id="result"> </h1>

<script>
	function calculate() {
	    let hoursRan = parseFloat(document.getElementById("inHoursRan").value);
	    let hourRate = parseFloat(document.getElementById("inHourRate").value);
        let ounceRate = parseFloat(document.getElementById("inOunceRate").value);
        let stroke = parseFloat(document.getElementById("inStroke").value);
        console.log(hoursRan +"/"+ hourRate +"*"+ ounceRate +"/"+ stroke);
        //main calculate result from input
		let pumps = hoursRan / hourRate * ounceRate / stroke;
		pumps = Math.round(pumps*1000, 2)/1000;
        document.getElementById("result").innerText= pumps+" pumps";
	    return false;
	}
</script>
<?php require "/var/www/php/footer.php";?>