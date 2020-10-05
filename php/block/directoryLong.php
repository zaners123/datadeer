<h4 style="margin-left:32px;text-align:center" id="list"> </h4>
<script id="script">
	let list = <?php
        require "/var/www/php/info.php";
        echo json_encode(getDirectoryList());
        ?>;
	let lastLetter = " ";
	let out = "";
	for (let item of list) {
	// console.log(item[1][0]+" "+item[1]);
	if (item[1][0] > lastLetter) {
		out+="<h1 style='font-family: cursive'>"+item[1][0]+"</h1>";
		lastLetter = item[1][0];
	}
	out+="<a href='"+item[0]+"'>"+item[1]+"</a><br>";
}
    document.getElementById("list").innerHTML = out;
    document.getElementById("script").innerHTML = "//Nothing to be seen here";
</script>