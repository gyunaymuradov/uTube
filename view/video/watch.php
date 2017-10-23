    <div class="col-md-10 text-left margin-5">
        <div class="row">
            <div class="col-md-8 thumbnail watch-height" id="<?= $params['video_id']; ?>">
                <video width="600" height="400" controls class="video-style">
                    <source src="<?= $params['video_url']; ?>" type="video/mp4">
                </video>
                <div class="row margin-left">
                    <div class="col-md-8">
                        <h3><?= htmlentities($params['video_title']); ?></h3>
                    </div>
                    <div class="col-md-4 margin-top">
                        <div class="btn-toolbar">
                            <button class="btn btn-info btn-lg" onclick="likeDislikeVideo(<?= $params['video_id']; ?>, 1)"><span class="glyphicon glyphicon-thumbs-up"></span>&nbsp;<span class="badge" id="video-like"><?= $params['likes']; ?></span></button>
                            <button class="btn btn-primary btn-lg" onclick="likeDislikeVideo(<?= $params['video_id']; ?>, 0)"><span class="glyphicon glyphicon-thumbs-down"></span>&nbsp;<span class="badge" id="video-dislike"><?= $params['dislikes']; ?></span></button>
                            <input type="hidden" id="logged" value="<?= $params['logged']; ?>">
                            <input type="hidden" id="logged-user-id" value="<?= $params['logged_user_id']; ?>">
                        </div>
                    </div>
                </div>
                <div class="row margin-left">
                    <div class="col-md-12">
                        <div><h4><?= htmlentities($params['video_description']); ?></h4></div>
                        <div><label>Uploaded by:&nbsp;&nbsp;</label><a href="index.php?page=user&id=<?= $params['uploader_id']; ?>"><?= htmlentities($params['uploader']); ?></a></div>
                        <div><label>Added On:&nbsp;&nbsp;</label><?= $params['date_added']; ?></div>
                    </div>
                </div>
                <button class='watch-bottom-btn btn btn-info' id='addToBtn<?= $params['video_id']; ?>' onclick='showHideAddTo(this.id)'>Add To</button>
                <div class='watch-bottom-div well-sm' style="display:none;" id='addToField<?= $params['video_id']; ?>'>
                    <p>Choose Playlist:</p>
                    <button class='btn btn-info margin-bottom-5 width-100' id='create<?= $params['video_id']; ?>' onclick='createPlaylist(this.id)'>Create New Playlist</button>
                    <div id='buttonContainer<?= $params['video_id']; ?>'></div>
                </div>
            </div>
            <div class="col-md-4 well pre-scrollable watch-height">
                <h4><?= $params['sidebar_title']; ?></h4>

                <?php

                $suggestedVideos = $params['suggested_videos'];
                if ($params['sidebar_title'] != 'Suggestions') {
                    $playlistId = $params['playlist_id'];
                    foreach ($suggestedVideos as $suggestedVideo) {
                        $title = $suggestedVideo['title'];
                        $videoId = $suggestedVideo['id'];
                        $videoThumbnail = $suggestedVideo['thumbnail_url'];
                        $uploader = htmlentities($suggestedVideo['username']);
                        $uploaderId = $suggestedVideo['uploader_id'];

                        echo "<div class='well-sm row bg-info'>
                                <div class='col-md-7'>
                                    <a href='index.php?page=watch&playlist-id=$playlistId&vid-id=$videoId'><img class='thumbnail-scrollbar' src='$videoThumbnail'></a>
                                </div> 
                                <div class='col-md-5 text-left no-padding suggestions-video-text'>
                                    <a href='index.php?page=watch&playlist-id=$playlistId&vid-id=$videoId'><small>$title</small></a><br>
                                    <a href='index.php?page=user&id=$uploaderId'><p><strong> $uploader</strong></p></a>
                                </div>
                            </div>";
                    }
                } else {
                    foreach ($suggestedVideos as $suggestedVideo) {
                        $title = htmlentities($suggestedVideo['title']);
                        $videoId = $suggestedVideo['video_id'];
                        $videoThumbnail = $suggestedVideo['thumbnail_url'];
                        $uploader = htmlentities($suggestedVideo['uploader_name']);
                        $uploaderId = $suggestedVideo['uploader_id'];

                        echo "<div class='well-sm row bg-info'>
                                <div class='col-md-7'>
                                    <a href='index.php?page=watch&id=$videoId'><img class='thumbnail-scrollbar' src='$videoThumbnail'></a>
                                </div> 
                                <div class='col-md-5 text-left no-padding suggestions-video-text'>
                                    <a href='index.php?page=watch&id=$videoId'><small>$title</small></a><br>
                                    <a href='index.php?page=user&id=$uploaderId'><p><strong>by $uploader</strong></p></a>
                                </div>
                            </div>";
                    }
                }

            ?>

            </div>
        </div>
        <div class="col-md-10 row">
            <h2>Comments</h2>
            <div class="form-group row">
                <div class="col-md-9">
                    <input type="text" id="comment-field" placeholder="Write a comment" class="form-control" maxlength="200">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-info btn-md form-control" onclick="comment(<?= $params['video_id']; ?>)">Comment</button>
                </div>
            </div>
            <div class="well-sm text-left col-md-12 row" id="comment-section">

                <?php

                    $commentsArr = $params['comments'];
                    /* @var $comment \model\Comment */
                    if (!empty($commentsArr)) {
                        foreach ($commentsArr as $comment) {
                            $username = htmlentities($comment->getCreatorUsername());
                            $commentText = htmlentities($comment->getText());
                            $dateAdded = $comment->getDateAdded();
                            $likes = $comment->getLikes();
                            $dislikes = $comment->getDislikes();
                            $commentId = $comment->getId();
                            $userId = $comment->getUserId();
                            $userPhoto = $comment->getCreatorPhoto();

                            echo "<div class='row bg-info margin-5 width-100'>
                                        <div class='col-md-8'>
                                            <img src='$userPhoto' class='img-circle margin-5' width='50' height='auto'>&nbsp;&nbsp;<label class='margin-5'><a href='index.php?page=user&id=$userId'>$username</a></label>
                                            <div class='well-sm width-100'>
                                               <p class='break-word'><strong>$commentText</strong></p>
                                               <small class='date_style'>$dateAdded</small>
                                            </div>
                                        </div>
                                        <div class='col-md-4 btn-toolbar '>
                                            <button class='btn btn-info btn-md col-lg-4 margin-comment-buttons' onclick='likeDislikeComment($commentId, 1)'><span class='glyphicon glyphicon-thumbs-up'>&nbsp;<span class='badge' id='comment-like-$commentId'>$likes</span></span></button>
                                            <button class='btn btn-primary btn-md col-lg-4 margin-comment-buttons' onclick='likeDislikeComment($commentId, 0)'><span class='glyphicon glyphicon-thumbs-down'>&nbsp;<span class='badge' id='comment-dislike-$commentId'>$dislikes</span></span></button>
                                        </div>
                                   </div>";
                        }
                    }

                ?>

            </div>
        </div>
    </div>