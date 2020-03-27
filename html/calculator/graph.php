<?php require "/var/www/php/header.php"; ?>
<title>Graphing Calculator</title>
<?php require "/var/www/php/bodyTop.php"; ?>
	<?php exit("DISABLED");?>

<div style="text-align: center">
<h1>Graphing Calculator&emsp;(<a href="index.php">Switch to Calculator</a>)

<!--

MAKE A Graphing Calculator (GRAPH)
If u do Daniel's math teacher will use it

WAY ONE: Generate an image, and send a link back

WAY TWO: Make a graphing function reader in javascript

WAY THREE: Make a graph function in PHP

WAY FOUR (GOOD):
	Make a (x,y) point list in Java, send it using PHP, and visually format it in RGraph

-->
<form id="formMain" onsubmit="return solve()">
	<!---Autofilled by addF();-->
</form>
<pre id="help"> </pre>
<canvas id="cvs" width="800" height="800">
	[Don't use internet explorer or put in a graph]
</canvas>
<br>

<!--Have four input fields for bounds, MINX, MAXX, MINY, MAXY-->
<table align="center">
	<tr>
		<td>Min X:<input id="minx" type="text" placeholder="Min X" value="-10" oninput="changedBounds()"></td>
		<td>Max X:<input id="maxx" type="text" placeholder="Max X" value="10" oninput="changedBounds()"></td>
	</tr>
	<tr>
		<td>Min Y:<input id="miny" type="text" placeholder="Min Y" value="-10" oninput="changedBounds()"></td>
		<td>Max Y:<input id="maxy" type="text" placeholder="Max Y" value="10" oninput="changedBounds()"></td>
	</tr>
</table>

</h1>
<script>
	let fCount = 0;
	function addF() {
	    fCount++;
	    let res = "";
	    for (let fc = 0; fc<fCount;fc++) {
	        //IF the graph function already exists, copy over the contents
	        if (document.getElementById("problem"+fc)) {
                res += "<input id=\"problem"+fc+"\" value='"+document.getElementById("problem"+fc).value+"' name=\"problem[]\" type=\"text\" placeholder=\"Problem\"><br>";
            } else {
	            //ELSE just make a new function
                res += "<input id=\"problem"+fc+"\" name=\"problem[]\" type=\"text\" placeholder=\"Problem\"><br>";
            }
        }
	    res+="<input type=\"submit\" value=\"Graph\">&emsp;";
        res+="<button onclick=\"addF()\">Add a Function</button>";
        document.getElementById("formMain").innerHTML = res;
	}
	addF();

	let cState = 0;
	let cTimer;
    function clock() {
        //alert(cState);
		cState++;
		let set = "";
		switch (cState) {
			case 0:set = "╔════╤╤╤╤════╗<br>" +
                         "║    │││ \\   ║<br>" +
                         "║    │││  O  ║<br>" +
                         "║    OOO     ║";break;
			case 1:set = "╔════╤╤╤╤════╗<br>" +
                         "║    ││││    ║<br>" +
                         "║    ││││    ║<br>" +
                         "║    OOOO    ║";break;
			case 2:set = "╔════╤╤╤╤════╗<br>" +
                         "║   / │││    ║<br>" +
                         "║  O  │││    ║<br>" +
                         "║     OOO    ║";break;
			default:
			       set = "╔════╤╤╤╤════╗<br>" +
                         "║    ││││    ║<br>" +
                         "║    ││││    ║<br>" +
                         "║    OOOO    ║";
			    cState = -1;
        }
        document.getElementById("help").innerHTML = set+'<br>';
    }
    let hidden;
	const size = 800;
    function solve() {
        //alert("SOLVING");
        cTimer = window.setInterval(clock, 400);
        clock();
	    let goHere = "calcBackend.php?type=g";
	    for (let prob = 0; prob < fCount; prob++) {
            let problem = document.getElementById("problem"+prob).value;
            console.log("PROB: "+problem);
            goHere+="&problem[]="+encodeURIComponent(problem);
        }
        //TODO in form send back variables for stateful calculator
        goHere+="&vars="+encodeURIComponent(hidden);
	     console.log(goHere);
        fetch(goHere, {credentials: "same-origin"}).then(function (response) {
            response.text().then(function (text) {
                graphRes = text.split(",");
                changedBounds();
                clearInterval(cTimer);
            });
        });
        return false;
    }
	let varRet = [];
	let graphRes = [];
	//set the hidden value in the form with vars (userset, xmin, etc)
	function setVars() {
	    let res = "";
	    //give it the vars
        for (let i = 0; i < varRet.length; i+=2) {
	        res+=varRet[i]+"="+varRet[i]+";";
        }
        res+="minx="+document.getElementById("minx").value+";";
        res+="miny="+document.getElementById("miny").value+";";
        res+="maxx="+document.getElementById("maxx").value+";";
        res+="maxy="+document.getElementById("maxy").value+";";
	    hidden = res;


	    //alert("SET"+res);
	    // document.getElementById("problem").value = res;
    }
    function drawCanvas() {
        if (graphRes.length === 0) {
            document.getElementById("help").innerHTML = 'Put in an equation such as "Y=X" or "sin(x)" and click "Graph"';
            return;
        } else {
            document.getElementById("help").innerHTML = '';
        }
        let canvas = document.getElementById("cvs");
        let ctx = canvas.getContext("2d");
        ctx.clearRect(0,0,canvas.width,canvas.height);
        ctx.beginPath();
        ctx.fillStyle = "black";
        let minX = parseInt(document.getElementById("minx").value);
        let minY = parseInt(document.getElementById("miny").value);
        let maxX = parseInt(document.getElementById("maxx").value);
        let maxY = parseInt(document.getElementById("maxy").value);
        ctx.lineWidth = 2;
        ctx.moveTo(size/2,0);
        ctx.lineTo(size/2,size);
        ctx.moveTo(0,size/2);
        ctx.lineTo(size,size/2);
        ctx.stroke();
        ctx.closePath();
		ctx.beginPath();
        ctx.lineWidth = 5;
        drawThisCanvas(ctx, minX, maxX, minY, maxY);
        ctx.stroke();
        ctx.closePath();
    }
    function drawThisCanvas(ctx, minX, maxX, minY, maxY) {
        ctx.moveTo(graphX(0, minX, maxX),graphY(1, minY, maxY));

        //alert(graphX(graphRes[0], minX, maxX));
        for(let i=0;i<graphRes.length;i+=2) {
            if (i>0 && graphX(i,minX,maxX) < graphX(i-2,minX,maxX)) {
                //move to the next spot (Used if you have two graphs for the skip back to the start)
                ctx.moveTo(graphX(i, minX, maxX),graphY(i+1, minY, maxY));
            } else {
                //keep drawing the graph
                ctx.lineTo(graphX(i, minX, maxX),graphY(i+1, minY, maxY));
            }
	    }

        //ctx.lineTo(size,size/2);
        //ctx.rect(minX, minY, maxX, maxY);
    }
    function graphX(preX, minX, maxX) {
	    return size*((parseFloat(graphRes[preX])-minX)/(maxX-minX));
    }
    function graphY(preY, minY, maxY) {
        return size - size*((parseFloat(graphRes[preY])-minY)/(maxY-minY));
    }
	function changedBounds() {
        setVars();
        drawCanvas();
	}
	changedBounds();
</script>
</div>
<?php require "/var/www/php/footer.php"; ?>