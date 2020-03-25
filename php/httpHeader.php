<?php
header("Deer: Beautiful");
header("X-Frame-Options: SAMEORIGIN");
header("X-XSS-Protection: 1; mode=block");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: no-referrer");
//delicious cookie, yum!
//$flavor=array("Chocolate_Chip","Frosted_Cookie","Iced_Cookie","Golden_Cookie","Burnt_Cookie","Gingerbread_Cookie","Peanutbutter_Cookie","Fortune_Cookie","Almond_Biscut","Butter_Pecan","Carrot_Cake","Oatmeal_Cookie","Christmas_Cookie","Short_Bread","Snickerdoodle","Sugar_Cookie");
//setcookie("delicious-cookie",$flavor[array_rand($flavor,1)]);