<?php
require "/var/www/php/info.php";
$things = getDirectoryList();
$thing = $things[array_rand($things)];
echo "<a href='$thing[0]'>".($thing[2]?:$thing[1])."</a>";