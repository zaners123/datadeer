<?php require "/var/www/php/header.php"; ?>
	<title>Share</title>
<?php require "/var/www/php/bodyTop.php"; ?>
<h1 class="c">View Public Files (or <a href="/share">Share your Files</a>)<br></h1>
<h3>
	Danger - The following could have been uploaded by anyone<br>
	If you see something illegal, <a href="/contact">Report it</a>
</h3>

<h2>
	<?php

	//a list of document ID's (each doc is a user)
	$db = getDatabase("share")["rows"];

	foreach ($db as $user) {
		//get each user's list of uploaded files
		$userfiles = sanitiseDoc(json_decode(getDocUnsafe("share",$user["id"]), true));
		foreach ($userfiles as $path=> $file) {
			//for each public file, print it
			if (isset($file["public"]) && $file["public"]=== "true") {
				echo $user["id"].": ";
				echo "<a href='https://datadeer.net/share/d.php?n=".urlencode($file["name"])."&q=".$path."'>";
				//	echo json_encode($file)."<br>";
				echo $file["name"];
				echo "</a><br>";
			}
		}
	}
	?>
</h2>
<?php require "/var/www/php/footer.php"; ?>