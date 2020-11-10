let msg = "";
//msg = "<a id='overlayMessage' href='https://www.realclearpolitics.com/epolls/2020/president/2020_elections_electoral_college_map.html'>Realtime Election Results</a>";
if (msg) {
	msg = "<div id='overlay'>"+msg+"</div>";
	document.body.innerHTML+=msg;
	document.body.style.marginTop = "100px";
}