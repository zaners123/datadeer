<?php require "/var/www/php/header.php"; ?>
	<title>Share</title>
<?php require "/var/www/php/bodyTop.php"; ?>
<h1>DataDeer Share</h1>
<?php require "aside.html"; ?>

DataDeer Share is for sharing any type of file! Imagine a combination of Audible, Youtube, and Google Drive; but better than all of them. This is that.

<script>
	function liked(filepath) {
	    console.log("like.php?n="+filepath);
        fetch("like.php?n="+filepath,{credentials: "same-origin"}).then(function (response) {
            response.text().then(function (response) {
                if (response==="LIKED") {
                    document.getElementById(filepath).style.color = "#F00";
                    document.getElementById("likecount-"+filepath).innerHTML++;
                } else if (response==="UNLIKED") {
                    document.getElementById(filepath).style.color = "#555";
                    document.getElementById("likecount-"+filepath).innerHTML--;
                }
                console.log(response);
            });
        });
	}
</script>

<span style="font-size: 32px;">
	<?php

	//TODO a list of files (make audio/video in a player) along with subscription and like buttons (like instagram)
	//      Give listed files a caption (stored also in CouchDB)

	//todo infinite scrolling
	//todo subscription tab and liked tab


	//a list of document ID's (each doc is a user)
	$db = getDatabase("share")["rows"];

	//todo buffer this to reload every minute somehow
	$posts = [];

	foreach ($db as $user) {
		//get each user's list of uploaded files
		$userfiles = sanitiseDoc(json_decode(getDocUnsafe("share",$user["id"]), true));
		foreach ($userfiles as $path=> $file) {
			//for each public file, print it
			if (isset($file["public"]) && $file["public"]=== "true") {
				$post["path"] = $path;
				$post["byuser"] = $user["id"];
				$post["name"] = $file["name"];
				if (!isset($file["likes_by"])) {
					$posts[0][] = $post;
				} else {
					$post["liked"] = isset($file["likes_by"][$_SESSION["username"]]);
					$posts[sizeof($file["likes_by"])][] = $post;
				}
			}
		}
	}
	ksort($posts);
	$posts = array_reverse($posts, true);
	//main print all sorted by likes
	foreach ($posts as $likes => $postsWithThatManyLikes) {
		foreach ($postsWithThatManyLikes as $post) {
			echo "<hr>";
			echo "<a href='https://datadeer.net/share/d.php?n=".urlencode($post["name"])."&q=".$post["path"]."'>";
			//	echo json_encode($file)."<br>";
			echo $post["name"];
			echo "</a>";
			echo " by <b>".$post["byuser"]."</b>";
			echo "<br><span id='likecount-". $post["path"] ."'>" . $likes ."</span>";
			echo "<a id='".$post["path"]."' style='color: "
				.   ($post["liked"]?"#F00":"#555")
				.   "' onclick='liked(\"".$post["path"]."\")'>&#x2764;</a>";
		}
	}

	?>
</span>
<?php require "/var/www/php/footer.php"; ?>