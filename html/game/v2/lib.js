function at(x,y) {
    return x + y*height;
}

function sendData(gametype, data, responseFunc) {
    let urlEncodedData = "",urlEncodedDataPairs = [],name;
    // Turn the data object into an array of URL-encoded key/value pairs.
    for( name in data ) {urlEncodedDataPairs.push( encodeURIComponent( name ) + '=' + encodeURIComponent( data[name] ) );}
    urlEncodedData = urlEncodedDataPairs.join( '&' ).replace( /%20/g, '+' );
    let url = "../request.php?gametype="+gametype+"&id="+boardId;
    //testing:
    // console.log("Sending to URL "+url);
    // console.log(urlEncodedData);
    fetch(url, {
        method: 'POST',
        cache: 'no-cache',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: urlEncodedData
    }).then(function (response) {
        response.text().then(responseFunc);
    });
}

function sendBoard(gametype) {
    console.log("sent: "+board.toString());
    sendData(gametype,
        {
            "board": board,
            "id": id,
        },
        function (newBoard) {
            console.log("Got back \"" + newBoard + "\"");
            let resp = JSON.parse(newBoard);
            respondToSendData(resp);
        }
    );
}

function getBoardNode(i) {
    if (isNaN(i)) return;
    if (i<0 || i>width*height) return;
    let node = boardViewer.children[0];
    // console.log("I="+i);
    // console.log(Math.floor(i/width)+" of1 "+node.children.length);
    node = node.children[Math.floor(i/width)];
    // console.log((i%width)+" of2 "+node.children.length);
    node = node.children[i%width];
    return node;
}

function setBoardCellClass(cellIndex,classSet,add = true) {
    if (!classSet) return;
    let n = getBoardNode(cellIndex);
    // n.className = '';
    if (add) {
        n.classList.add(classSet);
    } else {
        n.classList.remove(classSet);
    }
}

function getBoardHasClass(cellIndex, classSet) {
    if (!classSet) return false;
    let n = getBoardNode(cellIndex);
    return n.classList.contains(classSet);
}


function setBoardCell(i, val) {
    board = board.substr(0,i)+val+board.substr(i+1);
    let n = getBoardNode(i);
    n.innerHTML = val;
}

function setLocalBoard(toSet) {
    if (toSet.length !== width * height) alert("ERR bad size");
    for (let i = 0; i < width * height; i++) {
        setBoardCell(i,toSet[i]);
    }
}
function setStatus(val) {
    document.getElementById("status").innerHTML = val;
}