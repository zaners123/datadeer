<?php

function ended($id) {
	require_once "/var/www/php/couch.php";
	$doc = getDocUnsafe("gamematch","games");
	$doc[loadBoard($id)[0]]="x";
	setDoc("gamematch","games",$doc);
}
function saveBoard($id, $board) {
    //the board array should hold:
    //who's turn it is
    //one byte per spot on the grid
    $boardJSON = json_encode($board);
	$conn = mysqli_connect("localhost","website",parse_ini_file("/var/www/php/pass.ini")["mysql"],"userdata");
    //mysql> insert into chess (id,grid) values ("123123123123123","testgrid");
    $query = sprintf(
        'REPLACE into chess (id,grid) values ("%s","%s")',
        mysqli_real_escape_string($conn, $id),
        mysqli_real_escape_string($conn, $boardJSON)
    );
    mysqli_query($conn,$query);
    mysqli_close($conn);
}
function loadBoard($id) {
	$conn = mysqli_connect("localhost","website",parse_ini_file("/var/www/php/pass.ini")["mysql"],"userdata");

    $query = sprintf(
        'select * from chess where id="%s"',
        mysqli_real_escape_string($conn, $id)
    );

    $res = mysqli_query($conn,$query);
    if (mysqli_num_rows($res) > 0) {
        mysqli_close($conn);
        return ($res->fetch_assoc())["grid"];
    } else {
        mysqli_close($conn);
        return "nobadid";
    }
}