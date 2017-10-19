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
    document.getElementById("addToField" + Id).style.display = "none";
}

function deleteVideo(buttonId) {
    if (confirm("Are you sure you want to delete this video?")) {
        var videoId = buttonId.replace('delete', '');
        //implement AJAX delete here
    }
}

function showAddTo(buttonId) {
    var divId = buttonId.replace('addToBtn', 'addToField');
    var addToDiv = document.getElementById(divId);
    addToDiv.style.display = "block";
    var request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            var response = JSON.parse(this.responseText);
            var button;
            for (var i in response) {
                button = document.createElement("BUTTON");
                button.innerHTML = response[i]['title'];
                addToDiv.appendChild(button);
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
        alert(playlistTitle + "  " + videoId);
        var request = new XMLHttpRequest();
        request.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                var response = JSON.parse(this.responseText);
                alert(response['Result']);
            }
        };
        request.open('GET', 'http://localhost/uTube/root/index.php?page=playlist-create&title=' + playlistTitle + '&videoID=' + videoId);
        request.send("title=" + playlistTitle + '&videoId=' + videoId);
    }
}