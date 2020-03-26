<pre><?php

var_dump($a = preg_match("/^\d+x\d+,\d+$/","10x10,5"));
if (!$a) echo "NOT";