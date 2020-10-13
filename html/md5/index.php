<?php require "/var/www/php/header.php"; ?>
<title>MD5 Undoer</title>
<style>
    .space {
        margin: 4px;
    }
    .formbox{
        width: 35%;
        margin: 10% 5% 10% 5%;
        border: 5px solid black;
        display: inline-block;
    }
</style>
<script>
    function submitTo() {return submitIt(true);}
    function submitGet() {return submitIt(false);}

    /**@param toHash true if trying to make a hash*/
	function submitIt(toHash) {
		document.getElementById("result").innerHTML="Getting result...<br>Please wait...";
		let action = toHash?"toHash":"getHash";
		let inVal = document.getElementById(action).value;
	    let post = action+"="+inVal;
		fetch("api.php?"+post).then(function (response) {
		    response.text().then(function (jsonStr) {
			    let json = JSON.parse(jsonStr);
			    let set = "";
			    if (json.error) {
			    	set=json.error+" ("+inVal+")";
                } else if (toHash) {
			    	set = "The hash of "+inVal+" is '"+json.out+"'";
                } else {
				    if (!json.out.length) {
					    set = "Unknown hash '"+inVal+"'";
				    } else {
					    set = "The reverse hash of "+inVal+" is '";
					    for(let v of json.out) set+=v.in;
					    set+="'";
				    }
                }
			    document.getElementById("result").innerHTML = set;
		    });
	    });
	    return false;
    }
</script>
<?php require "/var/www/php/bodyTop.php"; ?>
<h1>MD5 hash stuff!</h1>
<div id="result"> </div>
<form class="formbox" style="float: left" method="post" onsubmit="return submitTo()">
    <label><h1>Get MD5 hash:</h1><input autocomplete="off" id="toHash" type="text" name="toHash" placeholder="To Hash, such as 'potatoes'"></label>
    <input class="space" type="submit" value="Hash">
</form>

<form class="formbox" style="float: right" method="post" onsubmit="return submitGet()">
    <label><h1>Undo MD5 hash:</h1><input autocomplete="off" id="getHash" type="text" name="getHash" placeholder="To Crack, such as 'bfaa8063e3a5067df30a18c75f51d4b9'"></label>
    <input class="space" type="submit" value="Undo">
</form>

