<?php
$zip = preg_replace("/[\D]/","",$_GET["zip"]);
$goto = "https://api.openweathermap.org/data/2.5/weather?zip=".$zip."&appid=1571c6621bd56787f40ec38633ce7f61";
//echo "http://api.openweathermap.org/data/2.5/weather?zip=".$zip;
echo file_get_contents($goto);