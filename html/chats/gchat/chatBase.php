<?php
//have to be signed in to access chat
$readOnlyDisabled = true;
require "/var/www/php/requireSignIn.php";
require "chatLib.php";


/**

 * How the table was made:
 * create table chat (id bigint not null auto_increment, username varchar(32) not null, room varchar(35) not null, text varchar(512) not null,primary key (id));
 * grant select on userdata.chat to 'website'@'localhost';
 * grant insert on userdata.chat to 'website'@'localhost';

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
