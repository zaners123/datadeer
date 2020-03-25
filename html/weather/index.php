<?php require "/var/www/php/header.php"; ?>
<title>Weather</title>
<?php require "/var/www/php/bodyTop.php"; ?>
<script>
    function trunc(md,num) {
        return (Math[num < 0 ? 'ceil' : 'floor'](num*md))/md;
    }
    function getWeather() {
        let zip = document.getElementById("zipInput").value;
        document.getElementById("frame").innerHTML="Getting weather...<br>Please wait...";
        fetch("weatherBase.php?zip="+zip).then(function (response) {
            response.text().then(function (jsonStr) {
                let set = "Weather retrieved for "+zip+":<br><br>";
                let json = JSON.parse(jsonStr);
                if (json.message === "city not found") set = "wrong zip code";

                let temp = Number.parseFloat(json["main"]["temp"]);
                //fahrenheit
                set += trunc(1000,((temp-273.15)*1.8)+32)+"&deg;F<br>";
                //celsius
                set += trunc(1000,(temp-273.15))+"&deg;C<br>";
                //kelvin
                set += trunc(1000,temp)+"K<br>";

                set += "<br>";

                //rain / snow
                for (let obj of json["weather"]) {
                    set += obj["description"] +"<br>";
                }

                //set+="<br><br>"+jsonStr;
                document.getElementById("frame").innerHTML = set;
            });
        });
    }
</script>
<h1 id="frame">
    For the current weather, please enter your ZIP code:<br>

    <form onsubmit="return getWeather()">
        <input id="zipInput" type="text" placeholder="Zip code"/>
        <button onclick="getWeather()">Get Weather</button> <br>
    </form>

</h1>



<h1>You can also use this weathermap:
	<script src='https://darksky.net/map-embed/@temperature,46.641,-100.149,4.js?embed=true&timeControl=true&fieldControl=true&defaultField=temperature&defaultUnits=_f'></script>
</h1>
<!--<h1>It is currently cold and might snow soon</h1>-->
<?php require "/var/www/php/footer.php"; ?>