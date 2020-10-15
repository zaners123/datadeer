<?php require "/var/www/php/header.php"; ?>
<title>MD5 Undoer</title>
<style>
    .space {
        margin: 4px;
    }
    .formbox{
        width: 100%;
        margin: 2% 2% 2% 2%;
        border: 5px solid black;
        display: inline-block;
    }
</style>
<script>
    function submitTo() {return submitIt(true);}
    function submitGet() {return submitIt(false);}

    /**@param toHash true if trying to make a hash*/
	function submitIt(toHash) {
	    let action = toHash?"toHash":"getHash";
	    let inVal = document.getElementById(action).value;
	    let post = action+"="+inVal;
	    document.getElementById("result").innerHTML=(toHash?("Hashing "+inVal+"..."):("Decrypting '"+inVal+"'...<br>This can take around 5-10 seconds..."))+"<br>Please wait...";
		fetch("api.php?"+post).then(function (response) {
		    response.text().then(function (jsonStr) {
			    let json = JSON.parse(jsonStr);
			    let set = "";
			    if (json.error) {
			    	set=json.error+" (Input:'"+inVal+"')";
                } else {
			    	if (toHash) {
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
    <label><h1>Do MD5 hash:</h1><input autocomplete="off" id="toHash" type="text" name="toHash" placeholder="To Hash, such as 'potatoes'"></label>
    <input class="space" type="submit" value="Hash This">


    <br><br>
    This is currently able to hash anything up to a max of 64 characters long
</form>

<form class="formbox" style="float: right" method="post" onsubmit="return submitGet()">
    <label><h1>Decrypt/Crack MD5 hash:</h1><input autocomplete="off" id="getHash" type="text" name="getHash" placeholder="To Decrypt, such as 'bfaa8063e3a5067df30a18c75f51d4b9'"></label>
    <input class="space" type="submit" value="Decrypt This">


    <br><br>
    This is currently able to crack 12,356,654 codes:
    <ul>
        <li>All lowercase-only messages up to 5 characters long</li>
        <li>Whatever people have thrown in the "Hash This" box</li>
    </ul>
</form>

