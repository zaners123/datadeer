<?php
//have to be signed in to access chat
$readOnlyDisabled = true;
require "/var/www/php/requireSignIn.php";
require "chatLib.php";


/**
 * How to add to table:
 * insert into chat (room,text) value ("general","Welcome to the General Chat!");
 * How to read table:
 * select room,text from chat;
 * */

//if they specify a room
if (isset($_GET["room"])) {

    //print_r($_GET);
    $room = preg_replace("/[\W]/","",$_GET["room"]);
    if (isset($_GET["update"]) && $_GET["update"]=="true") {
        //they are trying to update/read chat
        echo readChat($room);
    } else if (isset($_GET["chat"])) {
        //user sending a chat
        addChat($room, $_SESSION["username"],urldecode($_GET["chat"]));
        echo readChat($room);
    }
}
