function likeDislike(videoId, likeDislike) {
    var request = new XMLHttpRequest();
    var loggedUserId = document.getElementById('loggedUserId').value;
    var logged = document.getElementById('logged').value;
    if (logged === 'false') {
        alert('Please sign in to gain full access!');
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



