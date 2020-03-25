<?php require "/var/www/php/header.php" ?>
	<title>Mad Libs&#8482;</title>
<link href="https://fonts.googleapis.com/css?family=Indie+Flower" rel="stylesheet">
<style>
	.user {
		font-family: "Indie Flower";
		color: #9d0c1d;
		border-bottom: #000;
	}
</style>
<?php require "/var/www/php/bodyTop.php"; ?>
<script>
	let subs;
	let page;
	let allTerms;
    let formTerms = [];

    function getSubs() {
        fetch("backend.php?subs=true").then(function (response) {
            response.text().then(function (subsString) {
                subs = subsString.split("\n");
                let res="";
                for (let i = 0; i < subs.length; i++) {
                    res += "<a onclick='getLib("+i+");'>"+cfl(subs[i])+"</a><br>";
                }
                document.getElementById("subs").innerHTML = res;
            });
        });
	}

	function getLib(num) {
	    //alert("Getting page on "+subs[num]);
        fetch("backend.php?page="+encodeURIComponent(subs[num])).then(function (response) {
            response.text().then(function (pageRes) {
                //alert(page);
	            page = pageRes;
	            showForm();
            });
        });
    }

	function showForm() {
	    allTerms = page.split("$");
        for (let i = 1; i < allTerms.length; i+=2) {
            let term = allTerms[i];
            if (!(!new RegExp("/\d/").test(term) && formTerms.includes(term))) {
                formTerms.push(term);
            }
        }
        document.getElementById("body").innerHTML = "<form name='lib'>"

        for (let i = 0; i < formTerms.length; i++) {
            document.getElementById("body").innerHTML += cfl(formTerms[i])+"<input type='text' id='"+formTerms[i]+"'><br>";
        }
        document.getElementById("body").innerHTML += "<input type='submit' onclick='resform()' value='Submit'></form>";
    }

    function resform() {
        let formPair = [];
        for (let i = 0; i < formTerms.length; i++) {
            formPair[formTerms[i]] = document.getElementById(formTerms[i]).value;
            //alert(formTerms[i]+": "+document.getElementById(formTerms[i]).value);
        }

        let ret = "";
        for (let i = 0; i < allTerms.length; i++) {
            if (formTerms.includes(allTerms[i])) {
                ret += "<span class=user>"+formPair[allTerms[i]]+"</span>";
            } else {
                ret += allTerms[i];
            }
        }

        document.getElementById("body").innerHTML = ret;

        return false;
    }
    function cfl(str) {
        str = str.replace(/\d/,"");
        return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
    }
	getSubs();
</script>
<h1>




<div style="text-align: center" id="body">
	Choose a Story!
	<br><br>
	<span id="subs">Loading...</span>
</div>

<h3 style="text-align: center"><a href="howto.php">Submit your own!</a></h3>

</h1>
<?php require "..//var/www/php/footer.php"; ?>
