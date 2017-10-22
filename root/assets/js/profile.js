function getEditForm(userId) {
    var request = new XMLHttpRequest();
    var formDiv = document.getElementById('about');
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
        var navbar = document.getElementById('navbar');
        var request = new XMLHttpRequest();
        var subscribeBtn = document.getElementById('subscribe');
        request.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                var response = JSON.parse(this.responseText);
                var userPhoto = response['user-photo'];
                var username = response['username'];
                var subscribersHtml = document.getElementById('subscribers');
                subscribersHtml.innerHTML = '';
                subscribersHtml.innerHTML = response['subscribers'];
                if (subscribeBtn.innerHTML === 'Subscribe') {
                    subscribeBtn.innerHTML = 'Unsubscribe';
                    var a = document.createElement('a');
                    a.href = "index.php?page=user&id=" + profileId;
                    a.id = profileId;
                    a.innerHTML = "<div class='margin-5 width-100 text-left'><img src='" + userPhoto + "' class='img-circle subImg'> <label class='hiding'>&nbsp;&nbsp;" + username + "</label></div></a>";
                    navbar.appendChild(a);
                } else {
                    subscribeBtn.innerHTML = 'Subscribe';
                    var navbarElement = document.getElementById(profileId);
                    navbar.removeChild(navbarElement)
                }
            }
        };
        request.open('GET', 'http://localhost/uTube/root/index.php?page=subscribe&loggedId=' + loggedUserId + '&profileId=' + profileId);
        request.send();
    }
}

function getAboutPage(userId, delay) {
    var aboutPage = document.getElementById('about-page');
    if (aboutPage) {
        var url = 'http://localhost/uTube/root/index.php?page=about&id=' + userId;
        var request = new XMLHttpRequest();
        var aboutHtml = document.getElementById('about');

        request.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                var response = JSON.parse(this.responseText);
                var name = response['first_name'] + ' ' + response['last_name'];
                var email = response['email'];
                var dateJoined = response['date_joined'];
                var subscriptions = response['subscriptions'];
                var aboutHtmlContent = "<div class='col-md-3 col-md-offset-2'><h3 class='text-muted'>Name: </h3></div>";
                aboutHtmlContent += "<div class='col-md-4'><h3 class='text-muted'>" + name + "</h3></div>";
                aboutHtmlContent += "<div class='col-md-1 margin-top'><button class='btn btn-info' onclick='getEditForm(" + userId + ")'>Edit profile</button></div>";
                aboutHtmlContent += "<div class='col-md-3 col-md-offset-2'><h3 class='text-muted'>Email: </h3></div><div class='col-md-4'>";
                aboutHtmlContent += "<h3 class='text-muted'>" + email + "</h3></div>";
                aboutHtmlContent += "<div class='col-md-3 col-md-offset-2'><h3 class='text-muted'>Member since: </h3></div><div class='col-md-4'>";
                aboutHtmlContent += "<h3 class='text-muted'>" + dateJoined + "</h3></div>";
                aboutHtmlContent += "<div class='col-md-3 col-md-offset-2'><h3 class='text-muted'>Subscriptions: </h3></div>";
                aboutHtmlContent += "<div class='col-md-4'><h3 class='text-muted'>" + subscriptions + "</h3></div>";
                setTimeout(function () {
                    aboutHtml.innerHTML = aboutHtmlContent;
                }, delay);
            }
        };
        request.open('GET', url);
        request.send();
    }
}

function showVideoButtons(Id) {
    document.getElementById("edit" + Id).style.display = "block";
    document.getElementById("delete" + Id).style.display = "block";
    document.getElementById("addToBtn" + Id).style.display = "block";
}

function hideVideoButtons(Id) {
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
    else if(playlistTitle != null){
        var request = new XMLHttpRequest();
        request.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                var response = JSON.parse(this.responseText);
                if (!response['Result']) {
                    var playlistsContainer = document.getElementById('playlists');
                    var containerContents = "";
                    for (var i in response) {
                        containerContents += "<div class='col-md-3 margin-top' id='" + response[i]['id'] + "' onmouseenter='showPlaylistButtons(this.id)' onmouseleave='hidePlaylistButtons(this.id)'><a href='index.php?page=watch&playlist-id=$playlistId'> <img src='" + response[i]['thumbnailURL'] +"' class=\"img-rounded\" alt=\"\" width=\"100%\" height=\"auto\"> <h4 class='text-center text-muted' id='title" + response[i]['id'] + "'>" + response[i]['title'] + "</h4> </a> <button class='video-edit-btn btn btn-info' id='rename" + response[i]['id'] + "' onclick='renamePlaylist(this.id)'>Rename</button> <button class='video-delete-btn btn btn-info' id='removeVid" + response[i]['id'] + "' onclick='showRemoveVid(this.id)'>Remove Video</button> <div class='playlist-remove-div well-sm' id='removeField" + response[i]['id'] + "'> <p>Choose Video:</p> <div id='buttonContainer" + response[i]['id'] + "'></div> </div> </div>";
                    }
                    playlistsContainer.innerHTML = containerContents;
                    alert("Playlist created successfully. The video has been added in it.");
                }
                else {
                    alert(response['Result']);
                }
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

function showPlaylistButtons(Id) {
    document.getElementById("rename" + Id).style.display = "block";
    document.getElementById("removeVid" + Id).style.display = "block";
}

function hidePlaylistButtons(Id) {
    document.getElementById("rename" + Id).style.display = "none";
    document.getElementById("removeVid" + Id).style.display = "none";
    document.getElementById("removeVid" + Id).disabled = false;
    document.getElementById("removeField" + Id).style.display = "none";
    document.getElementById("buttonContainer" + Id).innerHTML = "";
}

function renamePlaylist(buttonId) {
    var playlistId = buttonId.replace('rename', '');
    var oldPlaylistTitle = document.getElementById("title" + playlistId).innerHTML;
    var newPlaylistTitle = prompt("Enter a new title for this playlist:", oldPlaylistTitle);
    if (newPlaylistTitle == "") {
        alert("You cant leave an empty field!");
    }
    else if(newPlaylistTitle != null){
        var request = new XMLHttpRequest();
        request.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                var response = JSON.parse(this.responseText);
                if (response['Result'] === "Playlist successfully renamed!") {
                    document.getElementById("title" + playlistId).innerHTML = newPlaylistTitle;
                }
                else {
                    alert(response['Result']);
                }

            }
        };
        request.open('GET', 'http://localhost/uTube/root/index.php?page=playlist-rename&playlistID=' + playlistId + '&newTitle=' + newPlaylistTitle);
        request.send();
    }
}

function showRemoveVid(buttonId) {
    document.getElementById(buttonId).disabled = true;
    var playlistId = buttonId.replace('removeVid', '');
    var divId = buttonId.replace('removeVid', 'removeField');
    var btnContId =  buttonId.replace('removeVid', 'buttonContainer');
    var removeDiv = document.getElementById(divId);
    var btnContainer = document.getElementById(btnContId);
    removeDiv.style.display = "block";
    var request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            var response = JSON.parse(this.responseText);
            var videoId;
            for (var i in response) {
                videoId = response[i]['id'];
                btnContainer.innerHTML += "<button class='btn btn-info margin-bottom-5' id='" + videoId + "|" + playlistId +"' onclick='removeVideo(this.id)'>" + response[i]['title'] + "</button>";
            }
        }
    };
    request.open('GET', 'http://localhost/uTube/root/index.php?page=get-playlist-videos&playlistID=' + playlistId);
    request.send();
}

function removeVideo(buttonId) {
    if (confirm("Are you sure you want to remove this video from the playlist?")) {
        var arrOfIds = buttonId.split("|");
        var videoId = arrOfIds[0];
        var playlistId = arrOfIds[1];
        var request = new XMLHttpRequest();
        request.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                var response = JSON.parse(this.responseText);
                alert(response['Result']);
                if (response['Result'] === 'Playlist deleted!') {
                    var playlist = document.getElementById(playlistId);
                    playlist.parentNode.removeChild(playlist);
                }
            }
        };
        request.open('GET', 'http://localhost/uTube/root/index.php?page=playlist-delete&playlistID=' + playlistId + '&videoID=' + videoId);
        request.send();
    }
}

function submitEditProfile() {
    var username = document.getElementById('username').value;
    var firstName = document.getElementById('first-name').value;
    var lastName = document.getElementById('last-name').value;
    var email = document.getElementById('email').value;
    var oldPass = document.getElementById('old-pass').value;
    var userId = document.getElementById('user-id').value;
    var newPass = document.getElementById('password').value;
    var newPassConfirm = document.getElementById('confirm-password').value;
    var request = new XMLHttpRequest();
    var params = "username=" + username + "&first_name=" + firstName + "&last_name=" + lastName + "&email=" + email + "&old_pass=" + oldPass + "&user_id=" + userId + "&new_pass=" + newPass + "&new_pass_confirm=" + newPassConfirm;

    request.onreadystatechange = function () {
        var formContainer = document.getElementById('about');
        if (this.readyState === 4 && this.status === 200) {
            formContainer.innerHTML = this.responseText;
        } else if (this.readyState === 4 && this.status === 304) {
            formContainer.innerHTML = getAboutPage(userId, 0);
            document.getElementById('username-old').innerHTML = username;
            }
        };

    request.open('POST', 'http://localhost/uTube/root/index.php?page=edit-profile', true);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.send(params);
}