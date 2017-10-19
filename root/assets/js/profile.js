function getEditForm(userId) {
    var request = new XMLHttpRequest();
    var formDiv = document.getElementById('menu2');
    request.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            formDiv.innerHTML = this.responseText;
        }
    };
    request.open('GET', 'http://localhost/uTube/root/index.php?page=edit-profile&id=' + userId);
    request.send();
}

function subscribe(profileId) {
    var logged = document.getElementById('logged').value;
    if (logged === 'false') {
        alert('Please sign in to gain full access!');
    } else {
        var loggedUserId = document.getElementById('loggedUserId').value;
        var request = new XMLHttpRequest();
        request.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                var subscribersCount = JSON.parse(this.responseText);
                var subscribersHtml = document.getElementById('subscribers');
                subscribersHtml.innerHTML = '';
                subscribersHtml.innerHTML = subscribersCount['subscribers'];
            }
        };
        request.open('GET', 'http://localhost/uTube/root/index.php?page=subscribe&loggedId=' + loggedUserId + '&profileId=' + profileId);
        request.send();
    }
}

function showButtons(Id) {
    document.getElementById("edit" + Id).style.display = "block";
    document.getElementById("delete" + Id).style.display = "block";
    document.getElementById("addToBtn" + Id).style.display = "block";
}

function hideButtons(Id) {
    document.getElementById("edit" + Id).style.display = "none";
    document.getElementById("delete" + Id).style.display = "none";
    document.getElementById("addToBtn" + Id).style.display = "none";
    document.getElementById("addToBtn" + Id).disabled = false;
    document.getElementById("addToField" + Id).style.display = "none";
    document.getElementById("buttonContainer" + Id).innerHTML = "";
}

function deleteVideo(buttonId) {
    if (confirm("Are you sure you want to delete this video?")) {
        var videoId = buttonId.replace('delete', '');
        var request = new XMLHttpRequest();
        request.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                var response = JSON.parse(this.responseText);
                if (response['Result'] === 'Success'){
                    var video = document.getElementById(videoId);
                    video.parentNode.removeChild(video);
                }
                else {
                    alert(response['Result']);
                }
            }
        };
        request.open('GET', 'http://localhost/uTube/root/index.php?page=delete-video&videoId=' + videoId);
        request.send();
    }
}

function showAddTo(buttonId) {
    document.getElementById(buttonId).disabled = true;
    var videoId = buttonId.replace('addToBtn', '');
    var divId = buttonId.replace('addToBtn', 'addToField');
    var btnContId =  buttonId.replace('addToBtn', 'buttonContainer');
    var addToDiv = document.getElementById(divId);
    var btnContainer = document.getElementById(btnContId);
    addToDiv.style.display = "block";
    var request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            var response = JSON.parse(this.responseText);
            var playlistId;
            for (var i in response) {
                playlistId = response[i]['id'];
                btnContainer.innerHTML += "<button class='btn btn-info margin-bottom-5' id='" + videoId + "|" + playlistId +"' onclick='insertVideo(this.id)'>" + response[i]['title'] + "</button>";
            }
        }
    };
    request.open('GET', 'http://localhost/uTube/root/index.php?page=get-playlist-names');
    request.send();

}

function createPlaylist(buttonId) {
    var videoId = buttonId.replace('create', '');
    var playlistTitle = prompt("Please enter the new playlist's title:");
    if (playlistTitle == "") {
        alert("You cant leave an empty field!");
    }
    else {
        var request = new XMLHttpRequest();
        request.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                var response = JSON.parse(this.responseText);
                alert(response['Result']);
            }
        };
        request.open('GET', 'http://localhost/uTube/root/index.php?page=playlist-create&title=' + playlistTitle + '&videoID=' + videoId);
        request.send();
    }
}

function insertVideo(btnId) {
    var arrOfIds = btnId.split("|");
    var videoId = arrOfIds[0];
    var playlistId = arrOfIds[1];
    var request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            var response = JSON.parse(this.responseText);
            alert(response['Result']);
        }
    };
    request.open('GET', 'http://localhost/uTube/root/index.php?page=playlist-insert&playlistID=' + playlistId + '&videoID=' + videoId);
    request.send();
}