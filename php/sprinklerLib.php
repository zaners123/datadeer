<?php
require_once "/var/www/php/couch.php";
define("SPRINKLER_DB","sprinkler");
define("SPRINKLER_USERS_DB","sprinkler_users");
/**--------------Device IDs----------------*/
function generateRandomID(): string {
    $length = 12;
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
function isValidID($id): bool {
    return preg_match("/^[0-9a-zA-Z]{12}$/",$id);
}
function isValidAutomaticName($id): bool {
    return isset($id) && preg_match("/^[0-9a-zA-Z _]{1,16}$/",$id);
}
/**-------------Device Management-------------*/
function getMyDevice($id) {
    if (isValidID($id) && in_array($id,getDoc(SPRINKLER_USERS_DB)["sprinklers"])) {
        return getDoc(SPRINKLER_DB,$id);
    }
    return null;
}
function saveDevice($id, $doc) {
    setDoc(SPRINKLER_DB,$id,$doc);
}
function bindDevice($id,$account) {
    $docAdd = getDoc(SPRINKLER_USERS_DB,$id);
    if (!isset($docAdd["sprinklers"])) $docAdd["sprinklers"]=array();
    $docAdd["sprinklers"][]=$id;
    setDoc(SPRINKLER_USERS_DB,$account,$docAdd);
}
function getMyDeviceList() {
    return getDoc(SPRINKLER_USERS_DB)["sprinklers"];
}
/**--------------Station management----------------*/
function getTimezoneList() {
    return DateTimeZone::listIdentifiers();
}
/**
 * @return int Timezone Offset from UTC in minutes
 */
function getTimezoneOffset($sprinklerDoc) {
    $tzDoc = new DateTimeZone($sprinklerDoc["tz"]?$sprinklerDoc["tz"]:"UTC");
    $tzUTC = new DateTimeZone("UTC");
    return $tzDoc->getOffset(new DateTime("now",$tzUTC))/60;
}
/**
 * Turns something like 13:00 into 60*13
 */
function getStartMinute($time) {
    return ((int)("".$time[0]))*600+((int)("".$time[1]))*60+((int)("".$time[3]))*10+((int)("".$time[4]));
}
function applyAutomaticTimers($doc, &$stations) {
    foreach ($doc["automatic"] as $key => &$auto) {
        //also test day before to prevent wrap over, such as starting at 11PM
        foreach (array(-24*60,0,24*60) as $minutesThroughCycle) {
            $minutesThroughCycle -= getStartMinute($auto["start"]);
            $minutesThroughCycle += getTimezoneOffset($doc);
            $minutesThroughCycle += (time()%86400)/60;
            error_log($auto["start"].'b'.$minutesThroughCycle);
            for ($x=0;$x<$doc["stations"];$x++) {
                $length = $auto[$x];
                if (0<$minutesThroughCycle && $minutesThroughCycle<$length) {
                    $stations[$x]=true;
                }
                $minutesThroughCycle -= $length;
            }
        }
    }
}
/**Always removes everything except the first requested station*/
function applyNeverRunMultiple($doc, &$stations) {
    if ($doc["never-multiple"]) {
        $foundOne = false;
        for($x=0;$x<sizeof($stations);$x++) {
            if ($stations[$x]) {
                if ($foundOne) $stations[$x]=false;
                $foundOne = true;
            }
        }
    }
}
function applyManualTimers($doc, &$stations) {
    for ($x=0;$x<$doc["stations"];$x++) {
        if (isset($doc["manual"][$x]) && $doc["manual"][$x] > $_SERVER["REQUEST_TIME"]) {
            $stations[$x]=true;
        }
    }
}
function getStations($id, $updateUptime=false): array {
    $doc = getDoc(SPRINKLER_DB,$id);
    if ($updateUptime) {
        $doc["last_update"] = $_SERVER["REQUEST_TIME"];
        saveDevice($id,$doc);
    }
    //start with low priority such as auto-settings, followed by manual settings, followed by hard locks (weather, etc)
//    error_log(json_encode($doc));
    //set number of stations
    $stations = array_fill(0,$doc["stations"],false);
    applyAutomaticTimers($doc,$stations);
    applyManualTimers($doc,$stations);
    applyNeverRunMultiple($doc,$stations);
    return $stations;
}
function printStations($id) {
    $stations = getStations($id,true);
    echo "ID".$id.'{';
    foreach ($stations as $s) {
        echo $s?'1':'0';
    }
    echo "}";
}