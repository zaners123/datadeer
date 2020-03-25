<?php require "/var/www/php/header.php"; ?>
<title>Finances - One Time</title>
<style>
	tr{
		margin: 10px;
	}
	th {
		font-weight: bold;
		border-bottom: 2px solid #000;
	}
	td {
		font-weight: bold;
		padding: 5px 10px 5px 10px;
	}
	.b {/*border*/
		border-collapse: collapse;
		border: 2px solid #000;
		text-align: center;
	}
</style>
<?php require "/var/www/php/bodyTop.php"; ?>

<div style="text-align: center">
	<h1>
		Finances - <?php
		//if reason = single
		if ($_GET["r"]==="s") {
			echo "One time (things you buy or spend once)<br>(Change to <a href=\"table.php?r=m\">Multiple";
		} else {
			echo "Repeating (things that keep happening to your money)<br>(Change to <a href=\"table.php?r=s\">Single";
		}
		echo "</a>";
		?>)
	</h1>
	<table id="main" class="b">
		<thead class="b">
			<tr>
				<td class="b">Category</td>
				<td class="b">Time</td>
				<td class="b">Title</td>
				<td class="b">Money</td>
				<td class="b">Description</td>
				<td class="b">Remove</td>
			</tr>
		</thead>
		<tbody id="tbod" class="b">

		</tbody>
	</table>

	<br>
	<hr>

	<!--Form for adding a row-->
	<form onsubmit="return addRow()">
		<h1>Add a Row:</h1>
		<table>
			<tr>
				<td>Category*</td><td><input id="category" class="required" type="text" placeholder="Category" name="category" required="required"></td>
				<td>Title</td><td><input id="title" type="text" placeholder="Title" name="title"></td>
			</tr>
			<tr>
				<td>Date</td><td><input id="date" type="date" placeholder="Date" name="date"></td>
				<td>Time</td><td><input id="daytime" type="time" placeholder="Time" name="daytime"></td>
			</tr>
			<tr><td>Money* (Positive=made,Negative=lost)</td><td><input id="money" class="required" type="number" placeholder="Money" step=".01" name="time" required="required"></td></tr>
			<tr><td>Description</td><td><input id="desc" type="text" placeholder="Description" name="desc"></td></tr>
		</table>
		<input type="submit" value="Add Row">
	</form>

	<script>
        function delRow(id) {
            document.getElementById("rownu"+id).outerHTML="";
            fetch("backend.php?r=<?php echo $_GET["r"]?>&remrow="+id,{credentials: "same-origin"}).then(function (response) {
                response.text().then(function (text) {
                    // alert("APPLY"+text);
                    applyJSON(JSON.parse(text));
                });
            });
        }
        function initLoad() {
            fetch("backend.php?r=<?php echo $_GET["r"]?>",{credentials: "same-origin"}).then(function (response) {
                response.text().then(function (text) {
                    // alert("APPLY"+text);
                    applyJSON(JSON.parse(text));
                });
            });
        }
        function applyJSON(res) {
            let table = document.getElementById("tbod");
            while (table.rows.length > 0) {
                table.deleteRow(0);
            }
            for (let key in res) {
                appendRowData(res[key]["id"],res[key]["category"],res[key]["time"],res[key]["title"],res[key]["amount"],res[key]["msg"]);
            }
        }
        function appendRowData(id,category,time,title,money,desc) {
            let table = document.getElementById("tbod");
            time = new Date(time*1000).toString();
            time = time.substring(0,time.indexOf("(")-1);
            money="$ "+money;
            table.innerHTML+="<tr id='rownu"+id+"'>"+
                "<td class='b'>"+category+"</td>"+
                "<td class='b'>"+time+"</td>"+
                "<td class='b'>"+title+"</td>"+
                "<td class='b'>"+money+"</td>"+
                "<td class='b'>"+desc+"</td>"+
                '<td onclick="delRow(\''+id+'\')" class="redText b">X</td></tr>';
        }
        function addRow() {
            let fetchURL =
                "backend.php?r=<?php echo $_GET["r"]?>&category="+encodeURIComponent(document.getElementById("category").value)
                +"&money="+encodeURIComponent(document.getElementById("money").value);

            let part = "&title="+document.getElementById("title").value;
            if (part!=="")fetchURL+=part;
            part = "&time="+encodeURIComponent(document.getElementById("date").value +" "+ document.getElementById("daytime").value);
            if (part!=="")fetchURL+=part;
            part = "&desc="+document.getElementById("desc").value;
            if (part!=="")fetchURL+=part;

            fetch(
                fetchURL,
                {credentials: "same-origin"}).then(function (response) {
                response.text().then(function (text) {
                    applyJSON(JSON.parse(text));
                });
            });
            return false;
        }
        // resetTable("mai n");
        initLoad();
        // window.setInterval(location.reload,15000);
        //alert(res);
	</script>
</div>
<?php require "/var/www/php/footer.php"; ?>