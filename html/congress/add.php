<h1>Add Question:</h1>
(Example: "Is your IP address APIPA (169.254.x.x)?","Yes","No","Apipa")<br>
(Example: "Are you having a networking problem?","Yes","No","Network")<br>
(Example: "Are you having a networking problem?","Yes","No","Network")<br>
(with-tag defaults to yes, without-tag defaults to no)<br>
<form>
	Question:<input type="text" name="question" placeholder="Question" required><br>
	With-Tag Answer:<input type="text" name="withTag" placeholder="With-Tag Answer"><br>
	Without-Tag Answer:<input type="text" name="withoutTag" placeholder="Without-Tag Answer"><br>
	Tag:<input type="text" name="tag" placeholder="Tag"><br>
	<input type="submit" value="Add Question"><br>
</form><br>
<hr>
<h1>Add Pairing:</h1>
(Example: "networking","id of solution to check ethernet cable")<br>
(Example: "bad cable","id of solution to check ethernet cable")<br>
(This is because one solution should have many related tags, on average about 5)
<form>
	Question tag (type of problem):<input type="text" list="questionTags" name="questionTag" placeholder="Question" required><br>
	Solution:<input type="text" list="solutions" name="solution" placeholder="Tag" required><br>
	<input type="submit" value="Add Pair">
</form>
<hr>
<h1>Add Solution:</h1>
<form>
	Solution:<input type="text" name="solution" placeholder="Solution" required><br>
	<input type="submit" value="Add Solution">
</form>
<hr>
<?php
function solutionToID($conn, $solution) {
	$query = sprintf("select id from con_solutions where text=\"%s\"", mysqli_real_escape_string($conn, $solution));
	return mysqli_fetch_assoc(mysqli_query($conn,$query))["id"];
}
function allSolutions($conn) {
	$query = sprintf("select text from con_solutions");
	return mysqli_query($conn,$query);
}
function allTags($conn) {
	$query = sprintf("select tag from con_problems");
	return mysqli_query($conn,$query);
}
$conn = mysqli_connect("localhost","con","AJSDKRHKAJBHRKASH$#Q(\$YIkhb895uhbA%U");
mysqli_select_db($conn,"userdata");
$query = "";
if (isset($_GET["question"]) && isset($_GET["tag"])) {
	//add question
	$query = sprintf("insert into con_problems (question,tag) values (\"%s\",\"%s\")",
		mysqli_real_escape_string($conn, $_GET["question"]),
		mysqli_real_escape_string($conn, $_GET["tag"]));
	mysqli_query($conn,$query);
	echo "added question ".$_GET["question"]." with tag ".$_GET["tag"];
} else if (isset($_GET["questionTag"]) && isset($_GET["solution"])) {
	//todo convert solution to solution_id
	$id = solutionToID($conn, $_GET["solution"]);
	//add pair
	$query = sprintf("insert into con_tagger (problem_id,solution_id) values (\"%s\",\"%s\")",
		mysqli_real_escape_string($conn, $_GET["questionTag"]),
		mysqli_real_escape_string($conn, $id));
	mysqli_query($conn,$query);
	echo "added pair ".$_GET["questionTag"]." to ".$_GET["solution"];
} else if (isset($_GET["solution"])) {
	//add solution
	$query = sprintf("insert into con_solutions (text) values (\"%s\")",
		mysqli_real_escape_string($conn, $_GET["solution"]));
	mysqli_query($conn,$query);
	echo "added solution ".$_GET["solution"];
}
//testing
echo $query;
echo "<datalist id='solutions'>";
$questions = allSolutions($conn);
while ($q = mysqli_fetch_assoc($questions)) {
	echo "<option>".$q["text"]."</option>";
}
echo "</datalist>";

echo "<datalist id='questionTags'>";
$questions = allTags($conn);
while ($q = mysqli_fetch_assoc($questions)) {
	echo "<option>".$q["tag"]."</option>";
}
echo "</datalist>";

mysqli_close($conn);
?>