function likeDislikeVideo(videoId, likeDislike) {
    var request = new XMLHttpRequest();
    var loggedUserId = document.getElementById('loggedUserId').value;
    var logged = document.getElementById('logged').value;
    if (logged === 'false') {
        alert('Please sign in/up in order to be able like or dislike videos.');
    } else {
        request.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                var likeHtml = document.getElementById('video-like');
                var dislikeHtml = document.getElementById('video-dislike');
                var response = JSON.parse(this.responseText);
                likeHtml.innerHTML = response['likes'];
                dislikeHtml.innerHTML = response['dislikes'];
            }
        };
        request.open('GET', 'http://localhost/uTube/root/index.php?page=like-video&like=' + likeDislike + '&video-id=' + videoId + '&user-id=' + loggedUserId);
        request.send();
    }
}

function likeDislikeComment(commentId, likeDislike) {
    var request = new XMLHttpRequest();
    var loggedUserId = document.getElementById('loggedUserId').value;
    var logged = document.getElementById('logged').value;
    if (logged === 'false') {
        alert('Please sign in/up in order to be able like or dislike comments.');
    } else {
        request.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                var likeHtml = document.getElementById('comment-like-' + commentId);
                var dislikeHtml = document.getElementById('comment-dislike-'  + commentId);
                var response = JSON.parse(this.responseText);
                likeHtml.innerHTML = response['likes'];
                dislikeHtml.innerHTML = response['dislikes'];
            }
        };
        request.open('GET', 'http://localhost/uTube/root/index.php?page=like-comment&like=' + likeDislike + '&comment-id=' + commentId + '&user-id=' + loggedUserId);
        request.send();
    }
}

function comment(videoId) {
    var request = new XMLHttpRequest();
    var loggedUserId = document.getElementById('loggedUserId').value;
    var logged = document.getElementById('logged').value;
    var commentText;
    if (logged === 'false') {
        alert('Please sign in/up in order to be able to comment videos.');
    } else {
        commentText = document.getElementById('comment-field').value; // input field value
        document.getElementById('comment-field').value = '';
        request.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                // gets the top comment of the section
                var commentSection = document.getElementById('comment-section');
                var topComment = commentSection.firstChild;

                var response = JSON.parse(this.responseText);
                var comment = response['comment'];
                var username = response['username'];
                var dateAdded = response['date'];
                var likes = response['likes'];
                var dislikes = response['dislikes'];
                var commentId = response['commentId'];
                var userId = response['userId'];
                var userPhoto = response['userPhoto'];


                var newCommentDiv = document.createElement('div');
                newCommentDiv.className = "row bg-info margin-5 width-100";

                var newComment = "<div class='col-md-9'>";
                newComment += "<img src='" + userPhoto + "' class='img-circle margin-5' width='50' height='auto'>&nbsp;&nbsp;<label class='margin-5'>";
                newComment += "<a href='index.php?page=user&id=" + userId + "'>" + username + "</a></label>";
                newComment += "<div class='well-sm''>";
                newComment += "<p><strong>" + comment + "</strong></p>";
                newComment += "<small class='date_style'>" + dateAdded + "</small></div></div>";
                newComment += "<div class='col-md-3 btn-toolbar'>";
                newComment += "<button class='btn btn-success btn-md col-lg-4 margin-comment-buttons' onclick='likeDislikeComment(" + commentId + ", 1)'>";
                newComment += "<span class='glyphicon glyphicon-thumbs-up'>&nbsp;<span class='badge' id='comment-like-" + commentId + "'>" + likes + "</span></span></button>";
                newComment += "<button class='btn btn-danger btn-md col-lg-4 margin-comment-buttons' onclick='likeDislikeComment(" + commentId + ", 0)'>";
                newComment += "<span class='glyphicon glyphicon-thumbs-down'>&nbsp;<span class='badge' id='comment-dislike-" + commentId + "'>" + dislikes + "</span></span></button></div>";

                newCommentDiv.innerHTML = newComment;
                commentSection.insertBefore(newCommentDiv, topComment);
            }

        };
            request.open('POST', 'http://localhost/uTube/root/index.php?page=comment', true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send("comment=" + commentText + '&videoId=' + videoId + '&userId=' + loggedUserId);

    }
}



