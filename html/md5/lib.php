<?php

/**
 * Add a string's hash to the database
 * @param $conn mysqli connection
 * @param $toAdd string with length < 64
 * @return string the hash
 */
function add($conn, $toAdd) {
    $hash = md5($toAdd);
    mysqli_query($conn,getSqlToAdd($conn,$toAdd));
    return $hash;
}

/**@return string SQL function to add a hash*/
function getSqlToAdd($conn, $input) {
    return sprintf('insert into hashes(`in`,`out`) values ("%s",unhex("%s"));',mysqli_real_escape_string($conn,$input),mysqli_real_escape_string($conn,md5($input)));
}

function get($conn, $hash) {
    $sql = sprintf('select `in` from hashes where `out`=unhex("%s")',mysqli_real_escape_string($conn,$hash));
    $sqlres = mysqli_query($conn,$sql);
    if (!$sqlres) return false;
    return $sqlres->fetch_all(MYSQLI_ASSOC);
}

/**
 * @param string $set something like "abcdefg"
 * @param int $sizeEach something like "2" if you want all 2-letter-pairs
 * @return Generator
 */
function arrayPermutation(string $set, int $sizeEach) {
    $size = strlen($set);
    $y = array_fill(0,$sizeEach,0);
    $yt = implode('',array_fill(0,$sizeEach,$set[0]));
    $x=0;
    while (true) {
        if ($y[$x]<$size) {
            $yt[$x]=$set[$y[$x]++];
            $x=0;
        } else {
            $y[$x]=0;
            $yt[$x]=$set[0];
            $x++;
            if ($x>=$sizeEach) return;
            continue;
        }
        yield $yt;
    }
}