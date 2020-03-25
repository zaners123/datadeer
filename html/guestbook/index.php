<?php
require "/var/www/html/chats/gchat/chatLib.php";
if (isset($_GET["update"])) {
	echo readChat("general");
	exit;
} else if (isset($_GET["chat"])) {
	addChat("general", "guestbook",urldecode($_GET["chat"]));
	echo readChat("general");
	exit;
}
?>
<?php require "/var/www/php/headerNoSignin.php"; ?>
	<title id="title">Guestbook</title>
	<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<?php require "/var/www/php/bodyTop.php"; ?>

	<div class="chat" id="chat">
		Loading...
	</div>
	<div class="footer">
		<h1>Welcome to the GuestBook! Say hi! </h1><h2><a href="/signup.php">Making an account</a> is free, BTW.</h2>
		<textarea class="searchInput leftSet" name="chattext" id="chattext" autofocus="true" rows="4" cols="40" placeholder="Message"></textarea>
		<button class="rightSet" onclick="sendChat()">Send</button>
	</div>
	<script>
        let user = "general";

        let lastText = "";
        function update() {
            fetch("index.php?update=true", {credentials: "same-origin"}).then(function (response) {
                response.text().then(function (text) {
                    if (text !== lastText) {
                        updateChat(text);
                        window.scrollTo(0,document.body.scrollHeight);
                        lastText = text
                    }
                });
            });
        }

        //update chat every couple seconds
        window.setInterval(update, 2000);
        update();

        function sendChat() {
            let msg = document.getElementById("chattext").value;
            if (msg.length===0) return;
            document.getElementById("chattext").value = "";
            //alert("send"+msg);
            fetch("index.php?chat="+encodeURIComponent(msg), {credentials: "same-origin"}).then(function (response) {
                response.text().then(function (text) {
                    if (text !== lastText) {
                        updateChat(text);
                        window.scrollTo(0,document.body.scrollHeight);
                        lastText = text;
                    }
                });
            });
        }
        //update the chat from the given server data
        function updateChat(text) {
            let chats = JSON.parse(text);
            let reshtml = "";
            for (let messageData of chats) {
                let msg = messageData["msg"].replace(/^\w*: /,"");

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