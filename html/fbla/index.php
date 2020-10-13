<?php require "localHeader.php"; ?>
<datalist id="citylist"> </datalist>

<script>
	let cities = <?php require "cities.json"; ?>;
	for (let city of cities) {
        document.getElementById("citylist").innerHTML += "<option value=\""+city[0]+","+city[1]+"\"> </option>"
    }
	function formVal(name) {
        return document.getElementsByName(name)[0].value;
	}
	function validate() {
	    let err = document.getElementById("err");
	    let validCities = true;

		if (document.getElementsByName("flight_from")[0]===formVal("flight_to")) {
            err.innerText="Flight From is same as To";
            return false;
		}

	    if (validCities) {
	        validCities = false;
            for (let city of cities) {
                if (city[0]+","+city[1] === formVal("flight_from")) {
                    validCities = true;
                    break;
                }
            }
        }

	    if (validCities) {
	        validCities = false;
            for (let city of cities) {
                if (city[0]+","+city[1] === formVal("flight_to")) {
                    validCities = true;
                    break;
                }
            }
        }

	    if (!validCities) {
	        err.innerText="Invalid city name.";
	        return false;
	    }

	    //check now < date_depart < date_return
		let nowMS = new Date().getTime() - 1000*60*60*24*2;
	    let departMS = new Date(document.getElementsByName("date_depart")[0].valueAsNumber).getTime();
	    let returnMS = new Date(document.getElementsByName("date_return")[0].valueAsNumber).getTime();
	    if (isOneWay()) returnMS = Infinity.valueOf();
	    if (nowMS >= departMS) {
            err.innerText="This would have already departed!";
            return false;
	    } else if (nowMS >= returnMS) {
            err.innerText="This would have already returned!";
            return false;
	    } else if (departMS > returnMS) {
            err.innerText="Return date is before departure. Try swapping them.";
            return false;
	    }

	    if (formVal("people_adults") + formVal("people_children") < 1) {
            err.innerText="You're booking for nobody! Have at least one adult or child.";
            return false;
	    }

	    //if reached, looks good
	    return true;
	}
	function isOneWay() {
        return document.getElementById("one-way-checkbox").checked;
	}
	function toggleReturn() {
		document.getElementById("toggleMe").style.visibility = (!isOneWay())?"visible":"hidden";
	}
</script>

<div id="err" class="red"></div>
<form autocomplete="off" method="post" action="schedule.php" onsubmit="return validate()">
	<table align="center">
		<tbody>
			<tr>
				<td>
					<label for="one-way-checkbox"><input name="oneway" id="one-way-checkbox" autocomplete="off" onclick="toggleReturn()" type="checkbox"> One Way</label><br>
				</td>
				<td>
					<label><input name="withCar" value="true" type="checkbox"> Rent a Car</label><br>
					<label><input name="withHotel" value="true" type="checkbox"> Find a hotel</label>
				</td>
			</tr>
			<tr>
				<td><label style="display:block;text-align: left">&emsp;From:<br><input list="citylist" name="flight_from" placeholder="From" value="Seattle,Washington" required></label></td>
				<td><label style="display:block;text-align: left">&emsp;To:<br><input list="citylist" name="flight_to" placeholder="To" value="Honolulu,Hawaii" required></label></td>
			</tr>
			<tr>
				<td><label style="display:block;text-align: left">&emsp;Departure Date:<br><input type="date" name="date_depart" value="2020-07-01" required></label></td>
				<td><label id="toggleMe" style="display:block;text-align: left">&emsp;Return Date:<br><input type="date" name="date_return" value="2020-07-06"></label></td>
			</tr>
			<tr>
				<td><label style="display:block;text-align: left">&emsp;Adults:<br><input type="number" min="0" max="10" name="people_adults" value="1" required></label></td>
				<td><label style="display:block;text-align: left">&emsp;Children:<br><input type="number" min="0" max="10" name="people_children" value="0" required></label></td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="submit" value="Find Flights" style="margin-top: 32px;padding: 4px">
				</td>
			</tr>
		</tbody>
	</table>
</form>

<h3>What is Deer Air?</h3>
<div>
	<p>Deer Air is your local friendly airline. We like planes and want you to, too.</p>
	<img src="https://media.giphy.com/media/jqO6kSMv3DXRC/giphy.gif">
</div>

<h3>Frequent Flyer</h3>
<div>
	<p>If you use our credit card, all of your flights are 25% off!</p>
</div>

<footer class="foot c"><span class="white"><a style="color: #fff" href="apply.php">Apply for a Job</a></span></footer>
</body>
</html>