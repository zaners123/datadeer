<?php require "/var/www/php/header.php"; ?>
	<title>To Do List</title>
	<style>
		tr{
			margin: 10px;
		}
		th {
			font-weight: bold;
			border-bottom: 2px solid #000;
		}
		td {
			border-bottom: 2px solid #ccc;
			font-size: 150%;
			font-weight: bold;
			padding: 5px 10px 5px 10px;
		}
	</style>
<?php require "/var/www/php/bodyTop.php"; ?>
<div class="center">

<h1 style="border-bottom: 2px solid #000">TODO</h1>

<table style="margin-bottom: 15%" align="center" id="main">
	<tbody id="tbod">

	</tbody>
</table>

<h1 style="border-top: 1px solid #000">Add to List:</h1>
<form class="center" onsubmit="return addRow()">
	<textarea id="title" rows="4" cols="40" autofocus="autofocus" class="center" placeholder="TODO" required="required" name="title"></textarea>
	<br>
	<input id="date" type="date" placeholder="Date" name="date"><input id="daytime" type="time" placeholder="Time" name="daytime"><br>
	<input type="submit" value="Add to list">
</form>
</div>

<script>
    function delRow(id) {
        document.getElementById("rownu"+id).outerHTML="";
        fetch("todoBackend.php?remrow="+id,{credentials: "same-origin"}).then(function (response) {
            response.text().then(function (text) {
                // alert("APPLY"+text);
                applyJSON(JSON.parse(text));
            });
        });
    }
    function initLoad() {
        fetch("todoBackend.php",{credentials: "same-origin"}).then(function (response) {
            response.text().then(function (text) {
                // alert("APPLY"+text);
                applyJSON(JSON.parse(text));
            });
        });
    }
    function applyJSON(res) {
        console.log("I got a JSON:");
        console.log(res);
        let table = document.getElementById("tbod");
        while (table.rows.length > 0) {
            table.deleteRow(0);
        }
        for (let key in res) {
            if (key==="x"||key[0]==="_") continue;
            appendRowData(res[key]["id"],res[key]["time"],res[key]["title"]);
        }
    }
    function formatDate(date) {
        return date.getMonth()+"/"+date.getDate()+"/"+date.getFullYear()
	        +" "+(date.getHours()%12)+":"+(date.getMinutes().toString().length===1?"0"+date.getMinutes():date.getMinutes())+" "+(date.getHours()>=12?"PM":"AM");
    }
    function appendRowData(id,time,title) {
        let table = document.getElementById("tbod");
        time = formatDate(new Date(time*1000));
        table.innerHTML+="<tr id='rownu"+id+"'>"+
            "<td>"+title+"</td>"+
            "<td>"+time+"</td>"+
            '<td onclick="delRow(\''+id+'\')" class="redText b">X</td></tr>';
    }
    function addRow() {
        let msg = document.getElementById("title").value;
        msg = msg.replace(/\n/g,"<br>");
        let fetchURL = "todoBackend.php?title="+msg;

        let part = encodeURIComponent(document.getElementById("date").value +" "+ document.getElementById("daytime").value);
        if (part!=="" && part.length>3)fetchURL+="&time="+part;

        console.log(fetchURL);
        fetch(fetchURL, {credentials: "same-origin"}).then(function (response) {
            response.text().then(function (text) {
                applyJSON(JSON.parse(text));
                document.getElementById("title").value = "";
            });
        });
        return false;
    }
    document.onkeypress = function(e) {
        let get = window.event?event:e;
        let key = get.keyCode?get.keyCode:get.charCode; //get character code
        if (!e.shiftKey && 13===key) {
            //alert("TEST");
            addRow();
        }
    };
    // resetTable("mai n");
    initLoad();
    // window.setInterval(location.reload,15000);
</script>
<?php require "/var/www/php/footer.php"; ?>