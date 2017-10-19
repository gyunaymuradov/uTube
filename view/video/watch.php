    <div class="col-md-10 justify-content-center text-left">
        <div class="row">
            <div class="col-md-8 thumbnail watch-height">
                <video width="600" height="400" controls class="video-style">
                    <source src="<?= $params['videoUrl']; ?>" type="video/mp4">
                </video>
                <div class="row margin-left">
                    <div class="col-md-8">
                        <h3><?= $params['videoTitle']; ?></h3>
                    </div>
                    <div class="col-md-4 margin-top">
                        <div class="btn-toolbar">
                            <button class="btn btn-success btn-lg" onclick="likeDislikeVideo(<?= $params['videoId']; ?>, 1)"><span class="glyphicon glyphicon-thumbs-up"></span>&nbsp;<span class="badge" id="video-like"><?= $params['likes']; ?></span></button>
                            <button class="btn btn-danger btn-lg" onclick="likeDislikeVideo(<?= $params['videoId']; ?>, 0)"><span class="glyphicon glyphicon-thumbs-down"></span>&nbsp;<span class="badge" id="video-dislike"><?= $params['dislikes']; ?></span></button>
                            <input type="hidden" id="logged" value="<?= $params['logged']; ?>">
                            <input type="hidden" id="loggedUserId" value="<?= $params['loggedUserId']; ?>">
                        </div>
                    </div>
                </div>
                <div class="row margin-left">
                    <div class="col-md-12">
                        <div><h4><?= $params['videoDescription']; ?></h4></div>
                        <div><label>Uploaded by:&nbsp;&nbsp;</label><a href="index.php?page=user&id=<?= $params['uploaderId']; ?>"><?= $params['uploader']; ?></a></div>
                        <div><label>Added On:&nbsp;&nbsp;</label><?= $params['dateAdded']; ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 well pre-scrollable watch-height">
                <h4><?= $params['sidebarTitle']; ?></h4>

                <?php

                $suggestedVideos = $params['suggestedVideos'];
                if ($params['sidebarTitle'] != 'Suggestions') {
                    $playlistId = $params['playlistId'];
                    foreach ($suggestedVideos as $suggestedVideo) {
                        $title = $suggestedVideo['title'];
                        $videoId = trim($suggestedVideo['id']);
                        $videoThumbnail = $suggestedVideo['thumbnail_url'];
                        $uploader = $suggestedVideo['username'];
                        $uploaderId = $suggestedVideo['uploader_id'];

                        echo "<div class='well-sm row bg-info'>
                                <div class='col-md-7'>
                                    <a href='index.php?page=watch&playlist-id=$playlistId&vid-id=$videoId'><img class='thumbnail-scrollbar' src='$videoThumbnail'></a>
                                </div> 
                                <div class='col-md-5 text-left no-padding suggestions-video-text'>
                                    <a href='index.php?page=watch&playlist-id=$playlistId&vid-id=$videoId'><small>$title</small></a><br>
                                    <a href='index.php?page=user&id=$uploaderId'><p><strong>By $uploader</strong></p></a>
                                </div>
                            </div>";
                    }
                } else {
                    foreach ($suggestedVideos as $suggestedVideo) {
                        $title = $suggestedVideo['title'];
                        $videoId = $suggestedVideo['video_id'];
                        $videoThumbnail = $suggestedVideo['thumbnail_url'];
                        $uploader = $suggestedVideo['uploader_name'];
                        $uploaderId = $suggestedVideo['uploader_id'];

                        echo "<div class='well-sm row bg-info'>
                                <div class='col-md-7'>
                                    <a href='index.php?page=watch&id=$videoId'><img class='thumbnail-scrollbar' src='$videoThumbnail'></a>
                                </div> 
                                <div class='col-md-5 text-left no-padding suggestions-video-text'>
                                    <a href='index.php?page=watch&id=$videoId'><small>$title</small></a><br>
                                    <a href='index.php?page=user&id=$uploaderId'><p><strong>By $uploader</strong></p></a>
                                </div>
                            </div>";
                    }
                }

            ?>

            </div>
        </div>

        <h2>Comments</h2>
        <div class="form-group row">
            <div class="col-md-10">
                <input type="text" id="comment-field" placeholder="Write a comment" class="form-control" maxlength="200">
            </div>
            <div class="col-md-2">
                <button class="btn btn-info btn-md form-control" onclick="comment(<?= $params['videoId']; ?>)">Comment</button>
            </div>
        </div>
        <div class="well-sm text-left" id="comment-section">

            <?php

                $commentsArr = $params['comments'];
                /* @var $comment \model\Comment */
                if (!empty($commentsArr)) {
                    foreach ($commentsArr as $comment) {
                        $username = $comment->getCreatorUsername();
                        $commentText = $comment->getText();
                        $dateAdded = $comment->getDateAdded();
                        $likes = $comment->getLikes();
                        $dislikes = $comment->getDislikes();
                        $commentId = $comment->getId();
                        $userId = $comment->getUserId();
                        $userPhoto = $comment->getCreatorPhoto();

                        echo "<div class='row bg-info margin-5 width-100'>
                                    <div class='col-md-9'>
                                        <img src='$userPhoto' class='img-circle margin-5' width='50' height='auto'>&nbsp;&nbsp;<label class='margin-5'><a href='index.php?page=user&id=$userId'>$username</a></label>
                                        <div class='well-sm'>
                                           <p><strong>$commentText</strong></p>
                                           <small class='date_style'>$dateAdded</small>
                                        </div>
                                    </div>
                                    <div class='col-md-3 btn-toolbar '>
                                        <button class='btn btn-success btn-md col-lg-4 margin-comment-buttons' onclick='likeDislikeComment($commentId, 1)'><span class='glyphicon glyphicon-thumbs-up'>&nbsp;<span class='badge' id='comment-like-$commentId'>$likes</span></span></button>
                                        <button class='btn btn-danger btn-md col-lg-4 margin-comment-buttons' onclick='likeDislikeComment($commentId, 0)'><span class='glyphicon glyphicon-thumbs-down'>&nbsp;<span class='badge' id='comment-dislike-$commentId'>$dislikes</span></span></button>
                                    </div>
                               </div>";
                        }
                    }

            ?>

        </div>
    </div>