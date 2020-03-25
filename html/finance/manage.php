<?php require "/var/www/php/headerNoSignin.php"; ?>
	<title>Finances - Manage</title>
<?php require "/var/www/php/bodyTop.php"; ?>

<h1>Manage your DataDeer Financial Data</h1>
<h2>The following is all of the NoSQL we have on you from in a JSON string.</h2>
<h2>This includes your DataDeer Finances, and your DataDeer Todo List data.</h2>
<hr>
<pre>
<?php require "/var/www/php/couch.php";
echo json_encode(sanitiseDoc(getDoc("finance",$_SESSION["username"],$fin)));?>
</pre>
<hr>

<h2>If you would like to export your DataDeer financial data, click <a href="export.php">Here</a></h2>

<hr>

<div style="font-size: 36px">If you would like to <b>IRREVERSIBLY</b> delete it ALL, please type "delete my data" into the box and click "I am sure".</div>
<form onsubmit="return killer();">
	<input id="delete" type="text" name="delete">
	<input type="submit" value="I am sure">
</form>
<script>
	function killer() {
        fetch("backend.php?delete="+document.getElementById("delete").value,{credentials: "same-origin"}).then(function (response) {
            response.text().then(function (text) {
                // alert("APPLY"+text);
                applyJSON(JSON.parse(text));
            });
        });
	    return false;
    }
</script>