<?php
if (isset($_FILES["userfile"])) {
	require "doglib.php";
	echo getPercentDog();
} else {?>
<h2>Hewwo to the DataDeer DogNet API</h2>
<pre>
Send a POST request containing the file you want to inspect under the POST name "userfile" to this URL, and I'll give you back its percent dog.

Make sure you pass it in the POST format multipart/form-data

Something like this properly uses the API:

&lt;form action="https://datadeer.net/dognet/api.php" enctype="multipart/form-data" method="post"&gt;
	&lt;input name="userfile" type="file" required=""&gt;
	&lt;input type="submit" value="Upload"&gt;
&lt;/form&gt;

</pre>
<?php }