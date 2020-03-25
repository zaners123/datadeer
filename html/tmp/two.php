<?php require "/var/www/php/header.php";?>
	<title>Learn Advanced HTML</title>
<?php require "/var/www/php/bodyTop.php";?>

YOUR COORDINATES: <span id="coord"> </span>
<br>
<canvas id="draw" width="1500" height="200" style="border: 2px black"> </canvas>

<video id="video" width="640" height="480" autoplay></video>
<button id="snap">Snap Photo</button>
<canvas id="canvas" width="640" height="480"></canvas>

<script>
	if (navigator.geolocation) {
	    navigator.geolocation.getCurrentPosition(function (pos) {
	        let out = document.getElementById("coord");
	        out.innerText = "("+pos.coords.latitude+","+pos.coords.longitude+")";
	    })
	}

	//canvas
	let c = document.getElementById("draw").getContext("2d");
	// c.arc(50,75,25,0,Math.PI*2);
	// c.arc(50,125,25,0,Math.PI*3);
	// c.lineTo(1450,125);
	// c.arc(1450,100,25,Math.PI/2,Math.PI*3/2,true);
	// c.lineTo(75,75);
	// c.stroke();

	//websocket
	/*let sock = new WebSocket("wss://datadeer.net");
	sock.onopen = function (event) {
		sock.send("This data isn't used");
    }*/

    var video = document.getElementById('video');
    if(navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        // Not adding `{ audio: true }` since we only want video now
        navigator.mediaDevices.getUserMedia({ video: true }).then(function(stream) {
            //video.src = window.URL.createObjectURL(stream);
            video.srcObject = stream;
            video.play();
        });
    }

</script>

<?php require "/var/www/php/footer.php";?>