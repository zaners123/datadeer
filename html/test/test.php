<?php require "/var/www/php/header.php"; ?>
	<title>IQ Test</title>
<?php require "/var/www/php/bodyTop.php"; ?>

<h1>Question Number <span id="qNumber"></span></h1>
<h2 id="question"></h2>
<h2 id="answers"></h2>
<button id="next" onclick="loadQuestion()">Next Question</button>

<form hidden="hidden" action="testscore.php" method="post" id="resultPoster">
	<input type="hidden" id="ansPost" name="ans" value="">
	<input type="hidden" id="ansType" name="type" value="">
</form>
<script>
	//their answers so far, sent at the end for a score. Send it as a JSON Array
	let testAnswers = [];

	let curQuestion = -1;

	/**If a question has no answers, it is a string answer. If it has answers, make a button for each*/
	let questions = [
	    <?php
	    if (!isset($_GET["type"])) return;
		if ($_GET["type"]=="iq") {?>
		/*0*/["What is ten to the power of three minus one? (Use PEMDAS)",99,100,999,27],
	        ["How many countries are there in North America?",2,1,39,3],
	        ["Door is to doorknob as car is to","Car Door","Car Seat","Trunk","Car Door Handle"],
	        ["What is the opposite of down","Here","Up","Next To","Upward"],
	        ["How many centimeters are there in a meter",100,10,1,1000],
		/*5*/["Where does Chocolate Milk come from?","Brown Cows","White Cows","Grey Cows","White cows with black spots"],
	        ["If 5 people are in a line how many possible orders are there for them to be in?",5,10,20,120],
	        ["If the blue man lives in the blue house, the yellow man lives in the yellow house, and the blue man lives in the blue house, who lives in the white house?"],
	        ["If a red house has red bricks and a blue house has blue bricks, what color bricks does a greenhouse have?"],
	        ["How much dirt is in a hole that measures two feet by three feet by four feet?"],
			/*10*/["If a fly has no wings would you call him a walk?","Yes","No, that's insulting","If he has walked","No, you call him a fly"],
	        ["Does England have a Fourth of July","Yes","No"],
	        ["How do you get off a nonstop flight?","Parachute","Jumping","A second plane","Balloons"],
	        ["What Question Number is this?",11,12,13,14],
	        ["If there are three apples and you took away two, how many do you have?",0,1,2,3,4],
		<?php }	else if ($_GET["type"]=="extend") { ?>
			/*0*/["Are dogs cute?","Yes","No"],
            ["What is sin(pi)","1","2","0","1/2","-1"],
	        ["What is the average IQ","1","100","110","Varies by Country"],
	        ["How many letters are in my name?","4","my name","6","my Daniel"],
	        ["How hot is the average guy","97.8","98.6","5/10","10/10","I'm straight"],
		    /*5*/["Type your answer to the last question, exactly. This question is worth double."],
	        ["What color are interstate shields?","Red","Blue","White","All of the above"],
	        ["What was the NMSL","National Maximum Sales Legislation","New Metric for Standard Law","National Maximum Speed Law","No Men See Ladies"],
	        ["What flavor is the sky?","Multiple","Blue","Purple","Unicorn","Red"],
	        ["How is the sky blue?","Refraction","Reflection","Scattering","Reduction","Food Crying"],
	        /*10*/["What is forward, backwards?","sdrawkcab","drawrof","Backwards Forwards","Stationary","darwrof"],
		<?php }	else if ($_GET["type"]=="comp") { ?>
			/*0*/["What is windows 10's minimum recommended ram usage?","1GB","2GB","4GB","8GB"],
			["How much CPU thermal paste is recommended?","A hair","A pea","A teaspoon","A tablespoon"],
			["How much power goes through PCIx16?","50W","55W","75W","100W"],
			["Which of these is closest to the storage size of a CD?","700KB","50MB","600mb","600MB","6000MB"],
			["What does ALT+F4 do usually","open command prompt","close command prompt","close a window","delete windows"],
			/*5*/["What is the best case fan orientation?","All fans pointed out","All fans pointed in","All fans pointed to the CPU","All fans facing the back"],
			["What does encryption do?","Guarantee message delivery","Guarantee security","Make hacking impossible","None of the above"],
			["Which of these likely comes separate with its fan?","CPU","GPU","DDR4 RAM","SSD"],
			["What is the average HDD RW speed?","500RPM","7200RPM","15000RPM","120RPS"],
			["Is security through obscurity safe?","Yes, always","No, never","It's better than no security"],
			/*10*/["Which of these is largest?","MATX","EATX","ATX"],
			["What is the storage of a 3.5\" floppy disk?","1MB","1.41MB","1.44MB","4.11MB","41.4MB"],
			["What is 2GB?","2048MB","1024TB","16MB","8GB"],
			["What is 2^10?","256","1024","2048","4096"],
			["How many bits is an IP address?","24","32","48","64"],
			/*15*/["What is ram?","Reliable access memory","Readable access memory","Rapid access memory","Random access memory"],
		<?php }	else if ($_GET["type"]=="spokane") { ?>
	        /*0*/["What does the name Spokane mean?","Run of the Sun","Sons of the Sun","Daughters of the Sun","Children of the Sun"],//Children of the Sun 4
	        ["What holiday is Spokane credited to being the home of?","Mother’s Day","Father’s Day","Grandparent’s Day","The Fourth of July","Indigenous People’s Day "],//Father’s Day
	        ["How many times has Spokane been voted All American?","3","2","too many times","none"],//3
	        ["What put Spokane on the map?","Interstate-90","Farming","Inland Empire Railroad","Expo ‘74","All of the Above"],//All of the Above
	        ["What was Expo ‘74?","The 1974 World’s Fair","The Expo of 1974","AgExpo -1 1974","You DON’T Want to Know"],//The 1974 World’s Fair
	        /*5*/["What is the population of Spokane?","209,000","217,000","435,000","501,000","720,000"],//217,000
	        ["What do Spokanite’s love?","Trucks","Weird Stuff","The Outdoors","All of the Above"],//All of the Above
	        ["Who designed the Davenport Hotel","Frank Lloyd Wright","Alvar Alto","Louis Sullivan","Kirtland Cutter","Richard Nuetra"],//Kirtland Cutter
	        ["What is the capacity of Spokane Veterans Memorial Arena?","14,000","12,146","12,638","9,780"],//12,638
	        ["How many times did the Spokane Shock win a championship?","12","1","5","9","0"],
	        /*10*/["Which of the following is not a nickname for Spokane?","Spokanistan","Spokompton","The Lilac City","Spokane Falls"],//Spokane Falls
	        ["What was the original name of Spokane?","Spokane","Spokane Falls","Spokane House","Fort Spokane","Lake Spokane"],//Spokane Falls
	        ["Who founded Spokane?","James Glover","Samuel Parker","George Wright","Jasper Mathaney"],//James Glover
	        ["Who was James Glover?","A fellow","A pioneer","A furtrapper","An investor","An Oregonian"],//An investor
	        ["How many lanes is Interstate-90 through Spokane?","4","3","2","5"],//3, 4, 2
	        /*15*/["What Major Highways run North and South in Spokane?","US 2, US 395","US 195, US 2","I-90, US 195","US 2, I-90","US 195, US 395"],
	        ["How many letters are in Spokane?","6","7","8","9","It doesn’t have any letters"],
	        ["What year was the Monroe Street Bridge constructed?","1898","1907","1911","1912","1923"],
	        ["By what means was Expo ’74 paid for through?","Taxes","Grants","Shady Business Deals","Outside Investors"],
	        ["What was Riverfront Park originally?","A railyard","A shipping center","A hydroelectric plant","An unusual thing","A train station"],
	        /*20*/["What was originally done where Riverpark Square Mall is now?","Court","Jailing","Eating at Restaurants","Executions"],
	        ["What is special about the Spokane County Court House?","The Design","The number of Courtrooms","The Size"],
	        ["Who is the mayor of Spokane?","Mary Verner","Cathy McMorris Rodgers","David Condon","Al French"],
		<?php } ?>
    ];

	function loadQuestion() {
	    //if you should collect input from last question
	    if (curQuestion !== -1) {
	        //collect input from last question
		    let ans;
		    if (questions[curQuestion].length === 1) {
			    ans = document.querySelector('input[name="ans"]');
                if (ans !== null && ans !== undefined && ans.value !== "") {
                    ans = ans.value;
                } else {
                    // if there is no user input do not continue!
                    //return;
                }
		    } else {
                ans = document.querySelector('input[name="ans"]:checked');
                if (ans !== null && ans !== undefined) {
                    ans = ans.value;
                } else {
                    // if there is no user input do not continue!
                    //return;
                }
		    }

		    if (questions[curQuestion].length === 1) {
		        //if its a fill-in-the-text question read that
                testAnswers.push(ans);
            } else {
		        //read the radio-button
                testAnswers.push(ans);
            }
		    //alert(JSON.stringify(testAnswers));
	    }


		curQuestion++;


	    if (curQuestion === questions.length) {
		    // window.location = "testscore.php?ans="+JSON.stringify(testAnswers);
		    document.getElementById("ansPost").value = JSON.stringify(testAnswers);
		    document.getElementById("ansType").value = "<?php echo $_GET["type"]?>";
		    document.getElementById("resultPoster").submit();
	    } else {

	        //make next question button say submit
            if (curQuestion === questions.length-1) {
                document.getElementById("next").innerHTML = "Submit Test";
            }

	        //load the question number
		    if ("iq" !== "<?php echo $_GET["type"]?>" || curQuestion!==13) {
		        document.getElementById("qNumber").innerHTML = (1+curQuestion).toString()+"/"+questions.length;
            } else {
                document.getElementById("qNumber").innerHTML = "???";
            }

            //load the current question
		    let q = questions[curQuestion];
		    //load the question
	        document.getElementById("question").innerHTML = q[0];

	        //list answers
            document.getElementById("answers").innerHTML ="<form>";
            if (q.length === 1) {
	            //fill-in-the-text question
                document.getElementById("answers").innerHTML +=
		            "<input type=\"text\" name=\"ans\">"
				;
            } else {
	            //radio-button-list
                for (let i = 1; i < q.length; i++) {
                    document.getElementById("answers").innerHTML +=
	                    "<div>"+
	                    "<input type=\"radio\" name=\"ans\" id=\""+i+"\" value=\""+i+"\">"+
                        "<label for=\""+i+"\">"+q[i]+"</label>"+
	                    "</div>"
                    ;
                }
	        }
            document.getElementById("answers").innerHTML +="</form>";
        }
        //alert(curQuestion+","+questions.length);
    }
	loadQuestion();
</script>
<?php require "/var/www/php/footer.php"; ?>