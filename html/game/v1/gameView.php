<?php
if (!isset($_GET["id"]) || !$_GET["id"]) {
    //redirect to no id if no id
	exit();
}
require "/var/www/php/header.php"
?>
    <title id="title">Game</title>
    <style id="styler">
        body {justify-content: center;text-align: center;}
        .grid {
            margin: 16px;
            justify-content: center;
            display: grid;
            grid-template-rows: 4rem 4rem 4rem 4rem 4rem 4rem 4rem 4rem 4rem 4rem;
	        font-family: 'Consolas', menlo, monospace;
	        font-size: 4rem;/*testing 1rem 4rem*/
	        text-align: center;
	        user-select: none;
        }
        .box {
            cursor: pointer;
        }
    </style>
	<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>

<!--Have it so this sends requests for moves to the server.
    The server then does the rest of the work in validating that (moveIsValid && isTheirTurn)
        If so, change the board based off of the move, and change who's turn it is.
        If the piece taken was a king, they win.-->
<?php require "/var/www/php/bodyTop.php"; ?>
    <h1 id="subtitle">Game Board</h1>
    <h2 id="status">Invalid ID or Enable JavaScript</h2>
If there are only boxes when you click, <a href="../other/settings.php">turn on the "use letters" setting</a>
	<div id="boardBox"><div id="grid" class="grid"></div></div>
    <script>
        let gameId = "<?php echo $_GET["id"]; ?>";
        let username = "<?php echo $_SESSION["username"]; ?>";
        let gameType = gameId.substring(0,1);
        let useLetters = <?php
	        require_once "/var/www/php/couch.php";
	        $doc = getDoc("profile",$_SESSION["username"],$blankDefault);
	        echo $doc["gameletter"]=="true"?"true":"false";
	        ?>;

        //set vars based off of game type
        let gameTypeLong;
        let gridLen;
        let colSize;
        switch (gameType) {
            // NOTE --- BASED OFF OF gameBase.php filename
            case "c":gridLen=64;gameTypeLong = "chess";colSize=8;break;
            // NOTE --- BASED OFF OF gameBase.php filename
            case "x":gridLen=64;gameTypeLong = "checkers";colSize=8;break;
            // NOTE --- BASED OFF OF gameBase.php filename
            case "f":gridLen=42;gameTypeLong = "connect";colSize=7;break;
            // NOTE --- BASED OFF OF gameBase.php filename
            case "b":gridLen=100;gameTypeLong = "battleship";colSize=10;break;
        }
        document.getElementById("title").innerHTML=capitalizeFirstLetter(gameTypeLong);
        let idlink = "<a href=\"https://datadeer.net/game/v2/join.php?id=<?=$_GET["id"]?>\"><?=$_GET["id"]?></a>";
        document.getElementById("subtitle").innerHTML=capitalizeFirstLetter(gameTypeLong)+" Board (Share ID '"+idlink+"')";

        //define column count
        if (gameType==="f") {
            //7 columns
	        document.getElementById("styler").innerHTML+= ".grid {grid-template-columns: 4rem 4rem 4rem 4rem 4rem 4rem 4rem;}";
        } else if (gameType==="b") {
            //10 columns and two spaced boards
            document.getElementById("styler").innerHTML+= ".grid {grid-template-columns: 4rem 4rem 4rem 4rem 4rem 4rem 4rem 4rem 4rem 4rem;}";

			document.getElementById("boardBox").innerHTML = "<table><tr>" +
                    "<td>Your Board<div id=\"grid\" class=\"grid\"></div></td>" +
                    "<td>Their Board<div id=\"gridOpp\" class=\"grid\"></div></td>" +
                "</tr></table>";
        } else {
            //8 columns
            document.getElementById("styler").innerHTML+= ".grid {grid-template-columns: 4rem 4rem 4rem 4rem 4rem 4rem 4rem 4rem;}";
        }

        //make the grid
        const $$ = s => document.querySelectorAll(s);
        const bg = "background-color: #ccF";
        for(let i=0;i<gridLen;i++) {
            if (isBGColored(i)){
                document.getElementById("grid").innerHTML+= "<div onclick='parseMove("+i+")' id=\"hole\" class=\"box\" data-index=\""+i+"\"></div>";
            } else {
                document.getElementById("grid").innerHTML+= "<div style='"+bg+"' onclick='parseMove("+i+")' id=\"hole\" class=\"box\" data-index=\""+i+"\"></div>";
            }
        }
        if (gameType==="b") {
            for(let i=0;i<gridLen;i++) {
                if (isBGColored(i)){
                    document.getElementById("gridOpp").innerHTML+= "<div onclick='parseMove("+i+")' id=\"hole\" class=\"box\" data-index=\""+(100+i)+"\"></div>";
                } else {
                    document.getElementById("gridOpp").innerHTML+= "<div style='"+bg+"' onclick='parseMove("+i+")' id=\"hole\" class=\"box\" data-index=\""+(100+i)+"\"></div>";
                }
            }
        }
        //make the board as an array
        let mainBoard;
        let pieceFrom = "";
        let pieceFromID = -1;
        let lastFrom = 0;
        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }
        function isBGColored(i) {
            if (colSize%2) {
                //odd boards
	            return i%2;
                //return i%colSize%2===0;
            } else {
                //even boards
                return (Math.floor(i/colSize)%2)===i%2;
            }
        }
        function parseMove(pieceUNFLIPPED) {
            if (gameType==="b") {
                sendMove(pieceUNFLIPPED);
            } else if (gameType==="f") {
                //piece flip is the piece's location with respect to the mainBoard flip
                //testing put back flip
                sendMove(pieceUNFLIPPED%7);
            } else {
                let piece = pieceUNFLIPPED;
                //piece flip is the piece's location with respect to the mainBoard flip
                //testing put back flip
                if (isOwner()) {piece = 63-piece}
                //move addr converts location 63 to coordinate (7,7)
                let moveAddr = piece%8 + "" + ((piece - (piece%8))/8);
                //if it's their piece, make it the "from"
                let piecesOfOwner = gameType==="c"?6:2;
                //if you are selecting a new piece of yours
	            if (gameType==="c") {

	                //this is for castling, woot.
                    if (pieceFrom!=="") {
                        if (isOwner()) {
                            //if the owner is trying to castle
                            if (mainBoard[piece + 2] === 1 && mainBoard[pieceFromID+2] === 5) {
                                sendMove(pieceFrom + moveAddr);
                            }
                        } else {
                            //if the opp is trying to castle
                            if (mainBoard[piece + 2] === 7 && mainBoard[pieceFromID+2] === 11) {
                                sendMove(pieceFrom + moveAddr);
                            }
                        }
                    }

                    if (mainBoard[piece+2]!==0 && (mainBoard[piece+2]>piecesOfOwner !== isOwner())) {
                        pieceFrom = moveAddr;
                        pieceFromID = piece;
                        if (!isBGColored(lastFrom)) {
                            $$('#hole')[lastFrom].setAttribute("style",bg);
                        } else {
                            $$('#hole')[lastFrom].removeAttribute("style");
                        }
                        lastFrom = pieceUNFLIPPED;
                        if (!isBGColored(lastFrom)) {
                            $$('#hole')[lastFrom].setAttribute("style",bg+";color:#F00;font-size:5rem;");
                        } else {
                            $$('#hole')[lastFrom].setAttribute("style","color:#F00;font-size:5rem;");
                        }
                    } else if (pieceFrom!=="") {
                        //if this is moving "to" somewhere and there is a viable from
                        sendMove(pieceFrom + moveAddr);
                    }

	            } else if (gameType==="x") {
                    if (mainBoard[piece+2]!==0 && (mainBoard[piece+2]>piecesOfOwner !== isOwner())) {
                        pieceFrom = moveAddr;
                        if (!isBGColored(lastFrom)) {
                            $$('#hole')[lastFrom].setAttribute("style",bg);
                        } else {
                            $$('#hole')[lastFrom].removeAttribute("style");
                        }
                        lastFrom = pieceUNFLIPPED;
                        if (!isBGColored(lastFrom)) {
                            $$('#hole')[lastFrom].setAttribute("style",bg+";color:#F00;font-size:5rem;");
                        } else {
                            $$('#hole')[lastFrom].setAttribute("style","color:#F00;font-size:5rem;");
                        }
                    } else if (pieceFrom!=="") {
                        //if this is moving "to" somewhere and there is a viable from
                        sendMove(pieceFrom + moveAddr);
                    }
	            }

            }
        }
        function sendMove(move) {
            fetch(gameTypeLong+"Base.php?id="+gameId+"&move="+move, {credentials: "same-origin"}).then(function (response) {
                response.text().then(function (text) {
                    $$('#hole')[lastFrom].removeAttribute("style");
                    updateBoard(text);
                });
            });
        }
        function fetchThenUpdateBoard() {
            fetch(gameTypeLong+"Base.php?id="+gameId,{credentials: "same-origin"}).then(function (response) {
                response.text().then(function (text) {
                    updateBoard(text);
                });
            });
        }
        function updateBoard(boardUpdate) {
            //testing all incoming data
            //alert("update\""+boardUpdate+"\"");
            if(boardUpdate.startsWith("no")) {
                if (boardUpdate==="nobadid") document.getElementById("status").innerHTML="Bad id";
                //testing updates
                //alert(boardUpdate);
	            return;
            }
            mainBoard = boardUpdate = JSON.parse(boardUpdate);
            let turnSet = boardUpdate[1];
            //if white won and you're the owner OR black won and you're not the owner


	        let yourBoatsPlaced = boardUpdate[isOwner()?202:203] >= 17;
	        let oppBoatsPlaced = boardUpdate[isOwner()?203:202] >= 17;

	        //document.getElementById("status").innerHTML = boardUpdate[202]+","+boardUpdate[203];

	        if (gameType!=="b" || (yourBoatsPlaced && oppBoatsPlaced)) {
                if ((turnSet==="WWIN") && isOwner() || (turnSet==="BWIN") && !isOwner()) {
                    document.getElementById("status").innerHTML="You Won";
                    //if white won and you're not the owner OR black won and you're the owner
                } else if ((turnSet==="WWIN") && !isOwner() || (turnSet==="BWIN") && isOwner()) {
                    document.getElementById("status").innerHTML="You Lost";
                } else if (turnSet===isOwner()) {
                    document.getElementById("status").innerHTML="Your turn";
                } else {
                    document.getElementById("status").innerHTML="Their turn";
                }
	        } else if (!yourBoatsPlaced && !oppBoatsPlaced) {
                document.getElementById("status").innerHTML="You and your opponent are placing boats";
            } else if (yourBoatsPlaced) {
                document.getElementById("status").innerHTML="Your opponent is placing his boats ("+boardUpdate[isOwner()?203:202]+"/17)";
            } else if (oppBoatsPlaced) {
                document.getElementById("status").innerHTML="You are placing your boats ("+boardUpdate[isOwner()?202:203]+"/17)";
            }

            $$('#hole').forEach((element, index) => {
                let piece;
                if (gameType==="f") {
                    piece = mainBoard[index+2];
                } else if (gameType==="b") {
                    if (isOwner()) {
                        piece = mainBoard[index+2];
                    } else {
                        if (index<100) {
                            piece = mainBoard[index+102];
                        } else {
                            piece = mainBoard[index-98];
                        }
                    }
                } else if (isOwner()) {
                    piece = mainBoard[65-index];
                } else {
                    piece = mainBoard[index+2];
                }
                //testing remove flip the board
                //piece = mainBoard[index+2];
	            if (useLetters) {
	                //USING LETTERS
                    if (gameType==="c") {
                        //chess pieces
                        switch (piece) {
                            case 5: piece = "K";break;
                            case 4: piece = "Q";break;
                            case 1: piece = "R";break;
                            case 3: piece = "B";break;
                            case 2: piece = "H";break;
                            case 6: piece = "P";break;
                            case 11: piece = "k";break;
                            case 10: piece = "q";break;
                            case 7: piece = "r";break;
                            case 9: piece = "b";break;
                            case 8: piece = "h";break;
                            case 12: piece = "p";break;
                        }
                    } else if (gameType==="x") {
                        //checkers pieces
                        switch (piece) {
                            case 1: piece = "w";break;//white single
                            case 2: piece = "W";break;//white stack

                            case 3: piece = "b";break;//black single
                            case 4: piece = "B";break;//black stack
                        }
                    } else if (gameType==="f") {
                        //Connect pieces
                        switch (piece) {
                            case 1: piece = "X";break;//white single
                            case 2: piece = "O";break;//black single
                        }
                    } else if (gameType==="b") {
                        /** CLIENT SIDE PIECES:
                         0 - Blank (on both boards)
                         1 - Boat (only sent for their side)
                         2 - Hit (on both boards)
                         3 - Miss (on both boards)
                         */
                        switch(piece) {
                            case 1: piece = "M";break;// miss
                            case 2: piece = "B";break;// placing boat
                            case 3: piece = "H";break;// collision symbol
                        }
                    }
	            } else {
                    if (gameType==="c") {
                        //chess pieces
                        switch (piece) {
                            case 5: piece = "&#9812;";break;
                            case 4: piece = "&#9813;";break;
                            case 1: piece = "&#9814;";break;
                            case 3: piece = "&#9815;";break;
                            case 2: piece = "&#9816;";break;
                            case 6: piece = "&#9817;";break;
                            case 11: piece = "&#9818;";break;
                            case 10: piece = "&#9819;";break;
                            case 7: piece = "&#9820;";break;
                            case 9: piece = "&#9821;";break;
                            case 8: piece = "&#9822;";break;
                            case 12: piece = "&#9823;";break;
                        }
                    } else if (gameType==="x") {
                        //checkers pieces
                        switch (piece) {
                            case 1: piece = "&#9920;";break;//white single
                            case 2: piece = "&#9921;";break;//white stack

                            case 3: piece = "&#9922;";break;//black single
                            case 4: piece = "&#9923;";break;//black stack
                        }
                    } else if (gameType==="f") {
                        //Connect pieces
                        switch (piece) {
                            case 1: piece = "&#9920;";break;//white single
                            case 2: piece = "&#9922;";break;//black single
                        }
                    } else if (gameType==="b") {
                        /** CLIENT SIDE PIECES:
                         0 - Blank (on both boards)
                         1 - Boat (only sent for their side)
                         2 - Hit (on both boards)
                         3 - Miss (on both boards)
                         */
                        switch(piece) {
                            case 1: piece = "&#10007;";break;// miss
                            case 2: piece = "&#9973;";break;// placing boat
                            case 3: piece = "&#128165;";break;// collision symbol
                        }
                    }
	            }

                // testing
                //element.innerHTML = (piece || '&#9744;')+":"+(index+2);
	            // normal
                element.innerHTML = piece || ''
            });
        }
        function isOwner() {
            return username===mainBoard[0];
        }
        fetchThenUpdateBoard();
        window.setInterval(updateLoop, 2000);
        function updateLoop() {
            //if there is no saved board or it is not your turn
            if ((!Array.isArray(mainBoard)) || isOwner()!==mainBoard[1]) {
                fetchThenUpdateBoard();
            }
        }
        document.getElementById("status").innerHTML="Unknown Problem (notify the owner with the game ID)";
    </script>
<?php require "/var/www/php/footer.php" ?>