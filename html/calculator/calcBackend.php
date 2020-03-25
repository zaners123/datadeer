<?php

require "/var/www/php/requireSignIn.php";



//type and problem need to be set
if (!isset($_GET["type"]) || !isset($_GET["problem"])) return;




//main set type and problem to be decoded
if ($_GET["type"]==="c") {
    /**res is the php array. It contains:
     *      0 - Problem Input (5*5)
     *      1 - Problem Output (25)
     *   >= 2 - Variable (x=5)
     *
     * EXAMPLE:
     *      IN: "x=5*5"
     *      OUT: "5*5","25","x=25"
     * */
    $query = "";
    //optionally give the "vars" variable
    if (isset($_GET["vars"])) {
    	$query .= $_GET["vars"].";";
    }
    $query .= "!;";

	foreach ($_GET["problem"] as $prob) {
		$query .= $prob.";";
	}

//echo $query;//testing remove

	//give it to ServerCalc.jar
	//      update this on Dev with Desktop/datadeer/updateCalc.sh
	$out = shell_exec("/var/www/WebCalc c ".escapeshellarg($query));



//	echo implode(",",$_GET["problem"]);
	if ($out !== "ERR") {
		echo $out;
	}
} else if ($_GET["type"]==="g" && isset($_GET["vars"])) {
	/**res is the php array. It contains:
	 *      0 - Problem Input (5*5)
	 *   >= 1 - X-Y Pairs
	 *
	 * EXAMPLE:
	 *      IN: "!;y=5*x;"
	 *      OUT: "5*x","0","0","1","5"
	 * */
	$query = $_GET["vars"]."!;";
	foreach ($_GET["problem"] as $prob) {
		$query.=$prob.";";
	}

	//give it to ServerCalc.jar
	//      update this on Dev with Desktop/datadeer/updateCalc.sh

	// Example output "let graph=[0,0,5,5]" for a line that goes from (0,0) to (5,5)
	$out = "ERR";
	//$out = shell_exec("/var/www/WebCalc g ".escapeshellarg($query));

	if ($out !== "ERR") {
		echo $out;
	}
}