<?php require "/var/www/php/header.php" ?>
<title>Sprinkler</title>
<?php require "/var/www/php/bodyTop.php"; ?>
<?php require_once "/var/www/php/sprinklerLib.php"; ?>
<div class="c">
<?php if ($doc = getMyDevice($_GET["s"])) {
    $id = $_GET["s"];
    //Basic Config
    if (isset($_POST["forming"])) {/*Since checkboxes give nothing when empty*/
        $doc["never-multiple"] = isset($_POST["never-multiple"]);
    }
    if(isset($_POST["stations"]))  $doc["stations"] = max(0,min((int)$_POST["stations"],12));
    if(isset($_POST["name"]))  $doc["name"] = htmlspecialchars($_POST["name"]);
    if(isset($_POST["tz"])) $doc["tz"] = $_POST["tz"];
    //Manual Enable
    for($x=0;$x<$doc["stations"];$x++) {
        $minutes = $_POST["manual".$x];
        if (isset($minutes) && ((float)$minutes)>0) {
            $doc["manual"][$x]=$_SERVER["REQUEST_TIME"]+$minutes*60;
        }
    }
    if (isValidAutomaticName($_POST["automatic_new"]) && sizeof($doc["automatic"])<50) {
        $doc["automatic"][$_POST["automatic_new"]] = array();
    }
    if (isValidAutomaticName($_POST["automatic_remove"])) {
        unset($doc["automatic"][$_POST["automatic_remove"]]);
    }
    foreach ($doc["automatic"] as $key=>&$auto) {
        $header = "automatic-".$key."-";
        $start = $_POST[$header."start"];
        if (isset($start)) $auto["start"] = $start;
        for($x=0;$x<$doc["stations"];$x++) {
            if (isset($_POST[$header.$x])) {
                $auto[$x] = $_POST[$header.$x];
            }
        }
    }
    //save changes
    saveDevice($id,$doc);
    //For Debugging / testing :
//    var_dump($doc);
    ?>
    <h1>Manage Sprinkler "<?=isset($doc["name"])?$doc["name"]:$id?>"</h1>
    <?php
    $status = getStations($id);
    for($x=0;$x<sizeof($status);$x++) {
        if ($status[$x])echo "<p style='background-color: #ff0'>Station " .($x+1)." is on</p>";
    }
    $timeSinceLastUpdate = $_SERVER["REQUEST_TIME"] - $doc["last_update"];
    if ($timeSinceLastUpdate<360) {
        echo "<p style='background-color: #0f0'>The device is currently online (last ping $timeSinceLastUpdate seconds ago)</p>";
    } else {
        echo "<p style='background-color: #f00'>Device may be offline. Last message ".$timeSinceLastUpdate. " seconds ago. (".round($timeSinceLastUpdate/3600/24,2)." days ago)</p>";
    }
    ?>
    <button onClick="window.location = window.location.href;">Refresh Page</button>
    <form class="align-left" method="post">
        <input type="hidden" name="forming" value="forming">
        <hr>
        <details>
            <summary class="font32">Basic Config</summary>
            <table>
                <tbody>
                    <tr><td class="align-right">Number of Stations (MUST match hardware!):</td><td><input name="stations" min="0" max="16" type="number" value="<?=isset($doc["stations"])?$doc["stations"]:0?>"></td></tr>
                    <tr><td class="align-right">Name:</td><td><input name="name" type="text" value="<?=isset($doc["name"])?$doc["name"]:""?>"></td></tr>
                    <tr><td class="align-right">Timezone (currently <?=getTimezoneOffset($doc)/60?>):</td><td><select name="tz" id="tz">
                        <?php foreach (getTimezoneList() as $tz) echo "<option ".(($tz==$doc["tz"])?"selected='selected'":"")."value='$tz'>$tz</option>"; ?>
                    </select></td></tr>
                    <tr><td class="align-right">Never Run Multiple Stations at the Same Time:</td><td><input name="never-multiple" type="checkbox" value="yes" <?=$doc["never-multiple"]=="yes"?"checked='checked'":""?>></td></tr>
                </tbody>
            </table>
            <button type="submit">Submit</button>
        </details>
        <hr>
        <details>
            <summary class="font32">Manual Run</summary>
            <table>
                <tbody>
                <?php for ($x=0;$x<$doc["stations"];$x++) {?>
                    <tr><td class="align-right">Station #<?=$x+1?>:</td><td>Run for <input name="manual<?=$x?>" type="number" min="0" max="360" step=".01" size="3"> minutes</td></tr>
                <?php }?>
                </tbody>
            </table>
            <button type="submit">Submit</button>
        </details>
        <hr>
        <details>
            <summary class="font32">Automatic Schedules</summary>
            <p>Add Automatic Schedule named <input name="automatic_new" type="text" maxlength="16" size="10" value=""><button type="submit">Add</button></p>
            <p>Remove Automatic Schedule named <input name="automatic_remove" type="text" maxlength="16" size="10" value=""><button type="submit">Remove</button></p>
            <hr>
            <?php foreach ($doc["automatic"] as $key => &$auto) {?>
                <h3><?=$key?></h3>
                <p>Start this at <input name="automatic-<?=$key?>-start" type="time" maxlength="16" size="10" value="<?=$auto["start"]?>"></p>
                <table>
                    <tbody>
                    <?php for ($x=0;$x<$doc["stations"];$x++) {?>
                        <tr><td class="align-right">Station #<?=$x+1?>:</td><td>Run for <input name="automatic-<?=$key?>-<?=$x?>" type="number" min="0" max="360" step=".01" size="3" value="<?=$auto[$x]?>">minutes</td></tr>
                    <?php }?>
                    </tbody>
                </table>
            <?php } ?>
            <button type="submit">Submit</button>
        </details>
    </form>
<?php } else { ?>
    <h1>Select your Sprinkler</h1>
    <?php if($_SESSION["username"]=="deer") {
        if(isset($_POST["sprinkler_id"]) && isset($_POST["sprinkler_account"])) {
            bindDevice($_POST["sprinkler_id"],$_POST["sprinkler_account"]);
            echo "Bound device ".$_POST["sprinkler_id"]." to account ".$_POST["sprinkler_account"]."<br>";
        }
        ?>
        <div class="useful_menu asideList">
            <h3>Admin Menu</h3>
            <form method="post">
                <p><label>Account:<input type="text" name="sprinkler_account" required></label></p>
                <p><label>Device ID:<input type="text" name="sprinkler_id" value="<?=generateRandomID();?>"></label></p>
                <button type="submit">Bind to account</button>
            </form>
        </div>
        <?php
    }
    foreach (getMyDeviceList() as $sprinkler) { ?>
        <a href="?s=<?=$sprinkler;?>">Open Sprinkler named <?=$sprinkler;?></a>
    <?php } ?>
<?php } ?>
</div>
<?php require "/var/www/php/footer.php" ?>