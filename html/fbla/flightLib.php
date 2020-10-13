<?php

function calcFlight(&$info) {
	$a = json_decode(file_get_contents("cities.json"),false);
	foreach ($a as $city) {
		if ($city[0].",".$city[1] === $info["flight_from"]) {
			$fromX = $city[8];
			$fromY = $city[9];
		} else if ($city[0].",".$city[1] === $info["flight_to"]) {
			$toX = $city[8];
			$toY = $city[9];
		}
	}
	$info["dist"] = pow(pow($fromX-$toX,2) + pow($fromY-$toY,2),.5);
	$info["price"] = round(sqrt($info["dist"])*15 + 100,2);
	$info["people"] = htmlspecialchars($info["people_adults"]+$info["people_children"]);
	$info["price"] *= $info["people"];
	if (isset($info["withHotel"])) $info["price"] += $info["people"] * 40;
	if (isset($info["withCar"])) $info["price"] += 40;
	if (isset($info["oneway"])) unset($info["date_return"]);
	if (isset($info["date_return"])) $info["price"] *= 2;

	//round into cents (do not use in any actual business scenario)
	$info["price"] = round($info["price"],2);

	$info["class"] = "Standard Class";

	//calc flight hours
	$flightHours = 1+round($info["dist"]/15,0);
	//depart time there
	$info["t1"]="12:00AM";
	//arrival time there
	$info["t2"]=$flightHours.":00PM";
	//depart time back
	$info["t3"]="4:00AM";
	//arrival time back
	$info["t4"]=($flightHours+4).":00AM";

	$hotelNames = ["Super Inn","Hotel Fancy","Suite Inn","Hot hotel"];
	$info["hotelName"]=$hotelNames[array_rand($hotelNames)];
	$carNames = ["Truck","SUV","Jeep","Old Green Car"];
	$info["carName"]=$carNames[array_rand($carNames)];


	if ($info["price"] <=0) {
		echo "ERR price <= 0";exit;
	}
}

function produceOffset($info) {
	$class = rand(0,10);
	if ($class<1) {
		$info["class"] = "Poor Class";
		$info["price"]*=.75;
	}else if ($class<5) {
		$info["class"] = "Standard Class";
	} else if ($class<8) {
		$info["price"]*=1.5;
		$info["class"] = "Second Class";
	} else {
		$info["price"]*=2;
		$info["class"] = "First Class";
	}

	$rand = rand(0,5);
	if ($rand==1) {
		//couldnt find hotel
		unset($info["withHotel"]);
		$info["price"]-=$info["peope"] *rand(20,40);
	} else if ($rand==2) {
		unset($info["withCar"]);
		$info["price"]-= rand(20,40);
	} else {
		//change price some
		$info["price"] *= rand(80,120)/100;
	}
	$info["price"] = round($info["price"],2);
	//parse and change date...
	return $info;
}

function printFlightInfo($info,$desc) {?>
	<div>A <?php if (isset($info["date_return"])) { ?>round trip<?php } ?> flight <?=$desc?></div>
	<div>from <i><?=htmlspecialchars($info["flight_from"]);?></i></div>
	<div>to <i><?=htmlspecialchars($info["flight_to"]);?></i></div>
	<div>with <?=$info["people"]?> <?=$info["people"]>1?"people":"person"; ?></div>
	<?php if ($info["withHotel"]) { ?> <div>with a hotel</div><?php } ?>
	<?php if ($info["withCar"]) { ?> <div>with a car</div><?php } ?>
	In <?=$info["class"];?>
	<div>for <b>$<?=htmlspecialchars($info["price"]);?></b></div>
<?php }