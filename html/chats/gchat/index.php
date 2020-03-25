<?php require "/var/www/php/header.php" ?>
    <title>Lobby</title>
    <script>
        function joinGame() {
            let gameId = document.getElementById("room").value;
            gameId = gameId.split(/[\W]/).join("").toLowerCase();
            window.location.replace("chatroom.php?room="+gameId)
        }
    </script>
<?php require "/var/www/php/bodyTop.php"; ?>
<h1 style="text-align: center">

    Group Chat (or go to <a href="/chats/pchat">Private chat</a>):
	<br><br>

	Enter a Chatroom:
    <input id="room" type="text" placeholder="Name"/>
    <button onclick="joinGame()">Enter Room</button><br>

	<br>

    Recommended chats
	<ul style="list-style: none;padding: 0;margin: 0;">
        <li><a href="chatroom.php?room=anonymous">Anonymous</a></li>
        <li><a href="chatroom.php?room=general">General</a></li>
        <li><a href="chatroom.php?room=minecraft">Minecraft</a></li>
        <li><a href="chatroom.php?room=tech">Technology</a></li>
    </ul>

	To make a chatroom, enter a unique name
</h1>
<?php require "/var/www/php/footer.php"; ?>