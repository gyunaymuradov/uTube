function getEditForm(userId) {
    var request = new XMLHttpRequest();
    var formDiv = document.getElementById('about');
    request.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            formDiv.innerHTML = this.responseText;
        }
    };
    request.open('GET', 'index.php?controller=user&action=edit&id=' + userId);
    request.send();
}

function subscribe(profileId) {
    var logged = document.getElementById('logged').value;
    if (logged === 'false') {
        alert('Please sign in to gain full access!');
    } else {
        var loggedUserId = document.getElementById('loggedUserId').value;
        var request = new XMLHttpRequest();
        var subscribeBtn = document.getElementById('subscribe');
        var subscribesSection = document.getElementById('subscribes-section');
        var subscribersCountSection = document.getElementById('subscribers');
        request.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                subscribesSection.innerHTML = '';
                subscribesSection.innerHTML = this.responseText;
                if (subscribeBtn.innerHTML === 'Subscribe') {
                    subscribersCountSection.innerHTML++;
                    subscribeBtn.innerHTML = 'Unsubscribe';
                } else {
                    subscribeBtn.innerHTML = 'Subscribe';
                    subscribersCountSection.innerHTML--;
                }
            }
        };
        request.open('GET', 'index.php?controller=user&action=subscribe&loggedId=' + loggedUserId + '&profileId=' + profileId);
        request.send();
    }
}

function getAboutPage(userId, delay) {
    var aboutPage = document.getElementById('about-page');
    if (aboutPage) {
        var url = 'index.php?controller=user&action=about&id=' + userId;
        var request = new XMLHttpRequest();
        var aboutHtml = document.getElementById('about');

        request.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                var response = JSON.parse(this.responseText);
                var username = response['username'];
                var userPhoto = response['userPhoto'];
                var subscribers = response['subscribers'];
                var name = response['first_name'] + ' ' + response['last_name'];
                var email = response['email'];
                var dateJoined = response['date_joined'];
                var subscriptions = response['subscriptions'];
                var aboutHtmlContent = "<div class='row margin-top'><div class='col-md-4 col-md-offset-1'>";
                aboutHtmlContent += "<img src='"+ userPhoto +"' class='img-rounded profile-img' height='auto'></div>";
                aboutHtmlContent += "<div class='col-md-4 col-md-offset-1 col-xs-4 col-xs-offset-1'>";
                aboutHtmlContent += "<h3 class='text-muted text-4vw' id='username-old'>" + username + "</h3></div>";
                aboutHtmlContent += "<div class='col-md-4 col-md-offset-1 col-xs-4'>";
                aboutHtmlContent += "<h3 class='text-muted text-4vw'>" + subscribers + " subscribers</h3></div></div>";
                aboutHtmlContent += "<div class='row'><div class='col-md-3 col-md-offset-2 col-xs-4 col-xs-offset-1'>";
                aboutHtmlContent += "<h3 class='text-muted text-4vw'>Name: </h3></div>";
                aboutHtmlContent += "<div class='col-md-4 col-xs-6'>";
                aboutHtmlContent += "<h3 class='text-muted text-4vw'>" + name + "</h3></div></div>";
                aboutHtmlContent += "<div class='row'><div class='col-md-3 col-md-offset-2 col-xs-4 col-xs-offset-1'>";
                aboutHtmlContent += "<h3 class='text-muted text-4vw'>Email: </h3></div>";
                aboutHtmlContent += "<div class='col-md-4 col-xs-6'>";
                aboutHtmlContent += "<h3 class='text-muted text-4vw'>" + email + "</h3></div></div>";
                aboutHtmlContent += "<div class='row'><div class='col-md-3 col-md-offset-2 col-xs-4 col-xs-offset-1'>";
                aboutHtmlContent += "<h3 class='text-muted text-4vw'>Member since: </h3></div>";
                aboutHtmlContent += "<div class='col-md-4 col-xs-6'>";
                aboutHtmlContent += "<h3 class='text-muted text-4vw'>" + dateJoined + "</h3></div></div>";
                aboutHtmlContent += "<div class='row'><div class='col-md-3 col-md-offset-2 col-xs-4 col-xs-offset-1'>";
                aboutHtmlContent += "<h3 class='text-muted text-4vw'>Subscriptions: </h3></div>";
                aboutHtmlContent += "<div class='col-md-4 col-xs-6'>";
                aboutHtmlContent += "<h3 class='text-muted text-4vw'>" + subscriptions + "</h3></div></div>";
                aboutHtmlContent += "<div class='row text-center margin-bottom-5'>";
                aboutHtmlContent += "<button class='btn btn-info' onclick='getEditForm(" + userId + ")'>Edit profile</button>";
                aboutHtmlContent += "</div></div>";

                setTimeout(function () {
                    aboutHtml.innerHTML = aboutHtmlContent;
                }, delay);
            }
        };
        request.open('GET', url);
        request.send();
    }
}

function showVideoButtons(videoId) {
    var id = videoId.replace('video', '');
    document.getElementById("edit" + id).style.display = "block";
    document.getElementById("delete" + id).style.display = "block";
    document.getElementById("addToBtn" + id).style.display = "block";
}

function hideVideoButtons(videoId) {
    var id = videoId.replace('video', '');
    document.getElementById("edit" + id).style.display = "none";
    document.getElementById("delete" + id).style.display = "none";
    document.getElementById("addToBtn" + id).style.display = "none";
    document.getElementById("addToBtn" + id).disabled = false;
    document.getElementById("addToField" + id).style.display = "none";
    document.getElementById("videoButtonContainer" + id).innerHTML = "";
}

function showAddButton(videoId) {
    var id = videoId.replace('video', '');
    document.getElementById("addToBtn" + id).style.display = "block";
}

function hideAddButton(videoId) {
    var id = videoId.replace('video', '');
    document.getElementById("addToBtn" + id).style.display = "none";
    document.getElementById("addToBtn" + id).disabled = false;
    document.getElementById("addToField" + id).style.display = "none";
    document.getElementById("videoButtonContainer" + id).innerHTML = "";
}

function deleteVideo(buttonId) {
    if (confirm("Are you sure you want to delete this video?")) {
        var id = buttonId.replace('delete', '');
        var videoId = buttonId.replace('delete', 'video');
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
        request.open('GET', 'index.php?controller=video&action=delete&videoId=' + id);
        request.send();
    }
}

function showAddTo(buttonId, page) {
    var btnAction = "";
    if (page === "user") {
        btnAction = "insertVideoFromOther";
    }
    else if (page === "profile") {
        btnAction = "insertVideo";
    }
    else {
        return;
    }
    document.getElementById(buttonId).disabled = true;
    var videoId = buttonId.replace('addToBtn', '');
    var divId = buttonId.replace('addToBtn', 'addToField');
    var btnContId =  buttonId.replace('addToBtn', 'videoButtonContainer');
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
                btnContainer.innerHTML += "<button class='btn btn-info margin-bottom-5 white-space width-100' id='" + videoId + "|" + playlistId +"' onclick='" + btnAction + "(this.id)'>" + response[i]['title'] + "</button>";
            }
        }
    };
    request.open('GET', 'index.php?controller=playlist&action=getNames');
    request.send();

}

function showHideAddTo(buttonId) {
    var videoId = buttonId.replace('addToBtn', '');
    var divId = buttonId.replace('addToBtn', 'addToField');
    var addToDiv = document.getElementById(divId);
    if (addToDiv.style.display === "none") {
        showAddTo(buttonId, "user");
        document.getElementById(buttonId).disabled = false;
    }
    else {
        document.getElementById("addToBtn" + videoId).disabled = false;
        document.getElementById("addToField" + videoId).style.display = "none";
        document.getElementById("videoButtonContainer" + videoId).innerHTML = "";
    }
}

function hideAddTo(divId) {
    document.getElementById(divId).style.display = "none";
    var btnContainerId = divId.replace('addToField', "videoButtonContainer");
    document.getElementById(btnContainerId).innerHTML = "";
}

function createPlaylist(buttonId) {
    var videoId = buttonId.replace('create', '');
    var playlistTitle = prompt("Please enter the new playlist's title:");
    if (playlistTitle == "") {
        alert("You can't leave an empty field!");
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
                        containerContents += "<div class='col-md-3 margin-top' id='playlist" + response[i]['id'] + "' onmouseenter='showPlaylistButtons(this.id)' onmouseleave='hidePlaylistButtons(this.id)'><a href='index.php?controller=video&action=watch&playlist-id=" + response[i]['id'] + "'> <img src='" + response[i]['thumbnailURL'] + "' class=\"img-rounded\" alt=\"\" width=\"100%\" height=\"auto\"> <h4 class='text-center text-muted' id='title" + response[i]['id'] + "'>" + response[i]['title'] + "</h4> </a> <button class='video-top-btn btn btn-info' id='rename" + response[i]['id'] + "' onclick='renamePlaylist(this.id)'>Rename</button><button class='video-middle-btn btn btn-info' id='deletePlaylist" + response[i]['id'] + "' onclick='deletePlaylist(this.id)'>Delete Playlist</button> <button class='video-bottom-btn btn btn-info' id='removeVid" + response[i]['id'] + "' onclick='showRemoveVid(this.id)'>Remove Video</button> <div class='video-bottom-div well-sm' id='removeField" + response[i]['id'] + "'> <p>Choose Video:</p> <div id='playlistButtonContainer" + response[i]['id'] + "'></div> </div> </div>";
                    }
                    if (playlistsContainer) {
                        playlistsContainer.innerHTML = containerContents;
                    }
                    alert("Playlist created successfully. The video has been added in it.");
                }
                else {
                    alert(response['Result']);
                }
            }
        };
        request.open('GET', 'index.php?controller=playlist&action=createPlaylist&title=' + playlistTitle + '&videoID=' + videoId);
        request.send();
    }
}

function createPlaylistFromOther(buttonId) {
    var videoId = buttonId.replace('create', '');
    var playlistTitle = prompt("Please enter the new playlist's title:");
    if (playlistTitle == "") {
        alert("You can't leave an empty field!");
    }
    else if(playlistTitle != null){
        var request = new XMLHttpRequest();
        request.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                var response = JSON.parse(this.responseText);
                if (!response['Result']) {
                    alert("Playlist created successfully. The video has been added in it.");
                }
                else {
                    alert(response['Result']);
                }
            }
        };
        request.open('GET', 'index.php?controller=playlist&action=createPlaylist&title=' + playlistTitle + '&videoID=' + videoId);
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
            if (!response['Result']) {
                var playlistsContainer = document.getElementById('playlists');
                var containerContents = "";
                for (var i in response) {
                    containerContents += "<div class='col-md-3 margin-top' id='playlist" + response[i]['id'] + "' onmouseenter='showPlaylistButtons(this.id)' onmouseleave='hidePlaylistButtons(this.id)'><a href='index.php?controller=video&action=watch&playlist-id=" + response[i]['id'] + "'> <img src='" + response[i]['thumbnailURL'] + "' class=\"img-rounded\" alt=\"\" width=\"100%\" height=\"auto\"> <h4 class='text-center text-muted' id='title" + response[i]['id'] + "'>" + response[i]['title'] + "</h4> </a> <button class='video-top-btn btn btn-info' id='rename" + response[i]['id'] + "' onclick='renamePlaylist(this.id)'>Rename</button><button class='video-middle-btn btn btn-info' id='deletePlaylist" + response[i]['id'] + "' onclick='deletePlaylist(this.id)'>Delete Playlist</button> <button class='video-bottom-btn btn btn-info' id='removeVid" + response[i]['id'] + "' onclick='showRemoveVid(this.id)'>Remove Video</button> <div class='video-bottom-div well-sm' id='removeField" + response[i]['id'] + "'> <p>Choose Video:</p> <div id='playlistButtonContainer" + response[i]['id'] + "'></div> </div> </div>";
                }
                playlistsContainer.innerHTML = containerContents;
                alert("Video successfully added!");
            }
            else {
                alert(response['Result']);
            }
        }
    };
    request.open('GET', 'index.php?controller=playlist&action=insertVideo&playlistID=' + playlistId + '&videoID=' + videoId);
    request.send();
}

function insertVideoFromOther(btnId) {
    var arrOfIds = btnId.split("|");
    var videoId = arrOfIds[0];
    var playlistId = arrOfIds[1];
    var request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            var response = JSON.parse(this.responseText);
            if (!response['Result']) {
                alert("Video successfully added!");
            }
            else {
                alert(response['Result']);
            }
        }
    };
    request.open('GET', 'index.php?controller=playlist&action=insertVideo&playlistID=' + playlistId + '&videoID=' + videoId);
    request.send();
}

function showPlaylistButtons(playlistId) {
    var id = playlistId.replace('playlist', '');
    document.getElementById("rename" + id).style.display = "block";
    document.getElementById("deletePlaylist" + id).style.display = "block";
    document.getElementById("removeVid" + id).style.display = "block";
}

function hidePlaylistButtons(playlistId) {
    var id = playlistId.replace('playlist', '');
    document.getElementById("rename" + id).style.display = "none";
    document.getElementById("deletePlaylist" + id).style.display = "none";
    document.getElementById("removeVid" + id).style.display = "none";
    document.getElementById("removeVid" + id).disabled = false;
    document.getElementById("removeField" + id).style.display = "none";
    document.getElementById("playlistButtonContainer" + id).innerHTML = "";
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
        request.open('GET', 'index.php?controller=playlist&action=renamePlaylist&playlistID=' + playlistId + '&newTitle=' + newPlaylistTitle);
        request.send();
    }
}

function showRemoveVid(buttonId) {
    document.getElementById(buttonId).disabled = true;
    var playlistId = buttonId.replace('removeVid', '');
    var divId = buttonId.replace('removeVid', 'removeField');
    var btnContId =  buttonId.replace('removeVid', 'playlistButtonContainer');
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
                btnContainer.innerHTML += "<button class='btn btn-info margin-bottom-5 white-space width-100' id='" + videoId + "|" + playlistId +"' onclick='removeVideo(this.id)'>" + response[i]['title'] + "</button>";
            }
        }
    };
    request.open('GET', 'index.php?controller=playlist&action=getVideos&playlistID=' + playlistId);
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
                if (!response['Result']) {
                    var playlistsContainer = document.getElementById('playlists');
                    var containerContents = "";
                    for (var i in response) {
                        containerContents += "<div class='col-md-3 margin-top' id='playlist" + response[i]['id'] + "' onmouseenter='showPlaylistButtons(this.id)' onmouseleave='hidePlaylistButtons(this.id)'><a href='index.php?controller=video&action=watch&playlist-id=" + response[i]['id'] + "'> <img src='" + response[i]['thumbnailURL'] + "' class=\"img-rounded\" alt=\"\" width=\"100%\" height=\"auto\"> <h4 class='text-center text-muted' id='title" + response[i]['id'] + "'>" + response[i]['title'] + "</h4> </a> <button class='video-top-btn btn btn-info' id='rename" + response[i]['id'] + "' onclick='renamePlaylist(this.id)'>Rename</button><button class='video-middle-btn btn btn-info' id='deletePlaylist" + response[i]['id'] + "' onclick='deletePlaylist(this.id)'>Delete Playlist</button> <button class='video-bottom-btn btn btn-info' id='removeVid" + response[i]['id'] + "' onclick='showRemoveVid(this.id)'>Remove Video</button> <div class='video-bottom-div well-sm' id='removeField" + response[i]['id'] + "'> <p>Choose Video:</p> <div id='playlistButtonContainer" + response[i]['id'] + "'></div> </div> </div>";
                    }
                    playlistsContainer.innerHTML = containerContents;
                    alert("Successfully removed video from playlist!");
                }
                else if (response['Result'] === 'Playlist deleted!') {
                    var playlist = document.getElementById('playlist' + playlistId);
                    playlist.parentNode.removeChild(playlist);
                }
                else {
                    alert(response['Result']);
                }
            }
        };
        request.open('GET', 'index.php?controller=playlist&action=removeVideo&playlistID=' + playlistId + '&videoID=' + videoId);
        request.send();
    }
}

function removeVideoInWatch(buttonId) {
    if (confirm("Are you sure you want to remove this video from the playlist?")) {
        var arrOfIds = buttonId.split("|");
        var playlistId = arrOfIds[0];
        var videoId = arrOfIds[1];
        var request = new XMLHttpRequest();
        request.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                var response = JSON.parse(this.responseText);
                if (!response['Result']) {
                    alert("Successfully removed video from playlist!");
                    var video = document.getElementById("video" + videoId);
                    video.parentNode.removeChild(video);
                }
                else if (response['Result'] === 'Playlist deleted!') {
                    alert("Playlist deleted!");
                    setTimeout(function () {
                        window.location.href = "index.php";
                    }, 100);
                }
                else {
                    alert(response['Result']);
                }
            }
        };
        request.open('GET', 'index.php?controller=playlist&action=removeVideo&playlistID=' + playlistId + '&videoID=' + videoId);
        request.send();
    }
}

function deletePlaylist(buttonId) {
    if (confirm("Are you sure you want to delete this playlist?")) {
        var id = buttonId.replace('deletePlaylist', '');
        var playlistId = buttonId.replace('deletePlaylist', 'playlist');
        var request = new XMLHttpRequest();
        request.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                var response = JSON.parse(this.responseText);
                if (response['Result'] === 'Success'){
                    var playlist = document.getElementById(playlistId);
                    playlist.parentNode.removeChild(playlist);
                }
                else {
                    alert(response['Result']);
                }
            }
        };
        request.open('GET', 'index.php?controller=playlist&action=deletePlaylist&playlistId=' + id);
        request.send();
    }
}

function submitEditProfile() {
    var username = document.getElementById('username').value;
    var firstName = document.getElementById('first-name').value;
    var lastName = document.getElementById('last-name').value;
    var email = document.getElementById('email').value;
    var userId = document.getElementById('user-id').value;
    var oldPass = document.getElementById('old-pass').value;
    var newPass = document.getElementById('password').value;
    var newPassConfirm = document.getElementById('confirm-password').value;
    var request = new XMLHttpRequest();
    var params = "username=" + username + "&first_name=" + firstName + "&last_name=" + lastName + "&email=" + email + "&old_pass=" + oldPass + "&user_id=" + userId + "&new_pass=" + newPass + "&new_pass_confirm=" + newPassConfirm;

    request.onreadystatechange = function () {
        var formContainer = document.getElementById('about');
        if (this.readyState === 4 && this.status === 200) {
            formContainer.innerHTML = this.responseText;
        } else if (this.readyState === 4 && this.status === 304) {
            document.getElementById('first-name-header').innerHTML = "Welcome, " + firstName + "!";
            formContainer.innerHTML = getAboutPage(userId, 0);
            }
        };

    request.open('POST', 'index.php?controller=user&action=edit', true);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.send(params);
}

var currentPage = 1;

function nextPage(param) {
    var pagesCount = document.getElementById('video-pages-count').value;
    if (currentPage < pagesCount) {
        currentPage++;
        if (param) {
            loadVideos(currentPage, 'user');
        } else {
            loadVideos(currentPage);

        }
    }
}

function previousPage(param) {
    if (currentPage > 1) {
        currentPage--;
        if (param) {
            loadVideos(currentPage, 'user');
        } else {
            loadVideos(currentPage);
        }
    }
}

function loadVideos(pageNumber, param) {
    var request = new XMLHttpRequest();
    var profileId = document.getElementById('current-profile').value;
    var url = 'index.php?controller=user&action=getVideos&pg=' + pageNumber + '&id=' + profileId;
    if (param) {
        url = 'index.php?controller=user&action=getVideos&param=user&pg=' + pageNumber + '&id=' + profileId;
    }
    var videosContainer = document.getElementById('videos-container');
    request.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            videosContainer.innerHTML = this.responseText;
        }
    };
    request.open('GET', url);
    request.send();
}

var currentPagePlaylists = 1;

function nextPagePlaylists(param) {
    var pagesCount = document.getElementById('playlist-pages-count').value;
    if (currentPagePlaylists < pagesCount) {
        currentPagePlaylists++;
        if (param) {
            loadPlaylists(currentPagePlaylists, 'user');
        }
    }
}

function previousPagePlaylist(param) {
    if (currentPagePlaylists > 1) {
        currentPagePlaylists--;
        if (param) {
            loadPlaylists(currentPagePlaylists, 'user');
        }
    }
}

function loadPlaylists(pageNumber, param) {
    var request = new XMLHttpRequest();
    var profileId = document.getElementById('current-profile').value;
    var url = 'index.php?controller=user&action=getPlaylists&pg=' + pageNumber + '&id=' + profileId;
    if (param) {
        url = 'index.php?controller=user&action=getPlaylists&param=user&pg=' + pageNumber + '&id=' + profileId;
    }
    var playlistsContainer = document.getElementById('playlists-container');
    request.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            playlistsContainer.innerHTML = this.responseText;
        }
    };
    request.open('GET', url);
    request.send();
}

var currentPageMostLiked = 1;

function nextMostLiked() {
    var pagesCount = document.getElementById('liked-pages-count').value;
    if (currentPageMostLiked < pagesCount) {
        currentPageMostLiked++;
        getMostLiked(currentPageMostLiked);
    }
}

function previousMostLiked() {
    if (currentPageMostLiked > 1) {
        currentPageMostLiked--;
        getMostLiked(currentPageMostLiked);
    }
}

function getMostLiked(page) {
    var request = new XMLHttpRequest();
    var url = 'index.php?controller=index&action=loadVideos&pg=' + page + '&row=1';
    var mostLikedContainer = document.getElementById('most-liked');
    request.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            mostLikedContainer.innerHTML = this.responseText;
        }
    };
    request.open('GET', url);
    request.send();
}

var currentPageNewest = 1;

function previousNewest(page) {
    if (currentPageNewest > 1) {
        currentPageNewest--;
        getNewest(currentPageNewest);
    }
}

function nextNewest(page) {
    var pagesCount = document.getElementById('newest-pages-count').value;
    if (currentPageNewest < pagesCount) {
        currentPageNewest++;
        getNewest(currentPageNewest);
    }
}

function getNewest(page) {
    var request = new XMLHttpRequest();
    var url = 'index.php?controller=index&action=loadVideos&pg=' + page + '&row=2';
    var newestVidContainer = document.getElementById('newest');
    request.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            newestVidContainer.innerHTML = this.responseText;
        }
    };
    request.open('GET', url);
    request.send();
}
