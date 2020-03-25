<?php require "/var/www/php/header.php" ?>
	<title>USERNAME - Chat</title>
<?php require "/var/www/php/bodyTop.php" ?>

<?php
//TODO check the username exists, if not go back to index.php
?>

<!--Chat goes here (main body of document)-->
<div class="chat" id="chat">Loading...</div>
<div class="footer">
	<textarea class="searchInput leftSet" name="chattext" id="chattext" autofocus="true" rows="4" cols="40" placeholder="Message"></textarea>
	<button class="rightSet" onclick="sendChat()">Send</button>
</div>
<script>
	let user = "<?php if (isset($_GET["user"])) {echo $_GET["user"];} ?>";
    let lastText = "";

    //code for chat interval update
    function update() {
        fetch("chatBase.php?user="+user, {credentials: "same-origin"}).then(function (response) {
            response.text().then(function (text) {
                if (text !== lastText) {
                    updateChat(text);
                    window.scrollTo(0,document.body.scrollHeight);
                    lastText = text;
                }
            });
        });
    }
    //update chat every couple seconds
    window.setInterval(update, 2000);
    update();

    function sendChat() {
        let msg = document.getElementById("chattext").value;
        while (msg[msg.length-1]==='\n') {
            msg = msg.substring(0, msg.length-1);
        }
        if (msg.length===0) return;
        document.getElementById("chattext").value = "";
        //alert("send"+msg);
        fetch("chatBase.php?user="+user+"&msg="+encodeURIComponent(msg), {credentials: "same-origin"}).then(function (response) {
            response.text().then(function (text) {
                if (text !== lastText) {
                    updateChat(text);
                    window.scrollTo(0,document.body.scrollHeight);
                    lastText = text;
                }
            });
        });
    }

	<?php
	//main used to make darkmode #000 turn into #fff

	require_once "/var/www/php/couch.php";
	$doc = getDoc("profile", $_SESSION["username"], $blankDefault);
	//main dark mode
	if (isset($doc["darkmode"]) && $doc["darkmode"] == "true") {
		echo 'let darkmode = "true"';
	} else {
		echo 'let darkmode = "false"';
	}

	?>



    //update the chat from the given server data
    function updateChat(text) {
        let chats = JSON.parse(text);
        let reshtml = "";
        for (let messageData of chats) {
            let msg = messageData["msg"].replace(/^\w*: /,"");

            //main convert darkmode #000 to #fff
            if (darkmode === "true" && messageData["color"]==="#000") {messageData["color"] = "#fff";}

            //make icon and line of colored chat
            reshtml+='<img width="50px" src="'+messageData["img"]+'"<br>';
            reshtml+="<span style=\"color:"+messageData["color"]+"\"><a style=\"color:"+messageData["color"]+"\" href='/user?user="+messageData["name"]+"'>"+messageData["name"]+": </a>"+msg+"<br></span>";
        }
        document.getElementById("chat").innerHTML = reshtml;
    }
    document.onkeyup = function(e) {
        let get = window.event?event:e;
        let key = get.keyCode?get.keyCode:get.charCode; //get character code
        if (!e.shiftKey && 13===key) {
            //alert("TEST");
            sendChat();
        }
    };
</script>
<?php //require "/var/www/php/footer.php"; ?>