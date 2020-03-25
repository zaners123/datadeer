<?php require "/var/www/php/header.php"; ?>
<title>Clock</title>
<?php require "/var/www/php/bodyTop.php"; ?>
<h1>Clock</h1>
Percent that the day is over: <span id="a"></span>%
<br><br>
Hours since 5AM: <span id="b"></span>
<br><br>
Seconds you spent on this page: <span id="c"></span>
<script>
    let d = new Date();
    let sh = d.getHours();let sm = d.getMinutes();let ss = d.getSeconds();let sms = d.getMilliseconds();
    function f() {
	    let d = new Date();
        let h = d.getHours();let m = d.getMinutes();let s = d.getSeconds();let ms = d.getMilliseconds();
        document.getElementById("a").innerHTML = (((((h*60)+m)*60)+s)*1000+ms)/864000;
        document.getElementById("b").innerHTML = h-5;
        let secondsOfDay = 0;
        document.getElementById("c").innerHTML = ((((((h*60)+m)*60)+s)*1000+ms)/864) - ((((((sh*60)+sm)*60)+ss)*1000+sms)/864);

	}
	window.setInterval(f,1);
	f();
</script>
<?php require "/var/www/php/footer.php"; ?>