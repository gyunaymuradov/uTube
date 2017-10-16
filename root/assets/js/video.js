function likeDislike(videoId, likeDislike) {
    var request = new XMLHttpRequest();
    var loggedUserId = document.getElementById('loggedUserId').value;
    var logged = document.getElementById('logged').value;
    if (logged === 'false') {
        alert('Please sign in/up in order to be able like or dislike videos.');
    } else {
        request.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                var likeHtml = document.getElementById('like');
                var dislikeHtml = document.getElementById('dislike');
                var response = JSON.parse(this.responseText);
                // alert(response['dislike'])
                likeHtml.innerHTML = response['likes'];
                dislikeHtml.innerHTML = response['dislikes'];
            }
        };
        request.open('GET', 'http://localhost/uTube/root/index.php?page=like-video&like=' + likeDislike + '&video-id=' + videoId + '&user-id=' + loggedUserId);
        request.send();
    }
}

function comment(videoId) {
    var request = new XMLHttpRequest();
    var loggedUserId = document.getElementById('loggedUserId').value;
    var logged = document.getElementById('logged').value;

    if (logged === 'false') {
        alert('Please sign in/up in order to be able to comment videos.');
    } else {
        var commentText = document.getElementById('commentText').value;

        if (this.readyState === 4 && this.status === 200) {
            var topComment = document.getElementById('top-comment');
            var response = JSON.parse(this.responseText);

            var comment = response['comment'];
            var username = response['username'];
            var dateAdded = response['date'];

            var commentContainer = document.createElement('div');
            commentContainer.className = 'row bg-info margin-5';

            commentContainer.id = 'top-comment';
            var commentUserDiv = document.createElement('div');
            commentUserDiv.className = 'col-md-10';

            var usernameHtml = document.createElement('label');
            usernameHtml.innerHTML = username;

            var commentDateDiv = document.createElement('div');
            commentDateDiv.className = 'well-sm';

            var commentP = document.createElement('p');
            commentP.innerHTML = comment;

            var dateP = document.createElement('p');
            dateP.className = 'date_style';
            dateP.innerHTML = dateAdded;

            commentDateDiv.appendChild(commentP);
            commentDateDiv.appendChild(dateP);

            var buttonsContainer = document.createElement('div');
            buttonsContainer.className = 'col-md-2';
            buttonsContainer.innerHTML = "<button class='btn btn-info btn-md col-lg-4 margin-comment-buttons'><span class='glyphicon glyphicon-thumbs-up'></span></button><button class='btn btn-info btn-md col-lg-4 margin-comment-buttons'><span class='glyphicon glyphicon-thumbs-down'></span></button>";

            commentUserDiv.appendChild(usernameHtml);
            commentUserDiv.appendChild(commentDateDiv);

            commentContainer.appendChild(commentUserDiv);
            commentContainer.appendChild(buttonsContainer);

            var commentSection = document.getElementById('comment-section');
            commentSection.insertBefore(commentContainer, topComment);

            // TODO fix comment box update!!!
        }

        request.open('POST', 'http://localhost/uTube/root/index.php?page=comment', true);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.send("comment=" + commentText + '&videoId=' + videoId + '&userId=' + loggedUserId);
    }
}



