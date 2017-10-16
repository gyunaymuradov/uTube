    <div class="col-md-10 justify-content-center text-center">
        <div class="row">
            <div class="col-md-8 thumbnail">
                <video width="600" height="400" controls class="video-style">
                    <source src="<?= $params['videoUrl']; ?>" type="video/mp4">
                </video>
                <div class="row">
                    <div class="col-md-8 text-left">
                        <div><h2><?= $params['videoTitle']; ?></h2></div>
                        <div><h4><?= $params['videoDescription']; ?></h4></div>
                        <div><label>Added On: </label><?= $params['dateAdded']; ?></div>
                        <div><label>Uploaded by: </label><?= $params['uploader']; ?></div>
                    </div>
                    <div class="col-md-4 btn-toolbar margin-top">
                        <button class="btn btn-success btn-lg" onclick="likeDislikeVideo(<?= $params['videoId']; ?>, 1)"><span class="glyphicon glyphicon-thumbs-up"></span>&nbsp;<span class="badge" id="video-like"><?= $params['likes']; ?></span></button>
                        <button class="btn btn-danger btn-lg" onclick="likeDislikeVideo(<?= $params['videoId']; ?>, 0)"><span class="glyphicon glyphicon-thumbs-down"></span>&nbsp;<span class="badge" id="video-dislike"><?= $params['dislikes']; ?></span></button>
                        <input type="hidden" id="logged" value="<?= $params['logged']; ?>">
                        <input type="hidden" id="loggedUserId" value="<?= $params['loggedUserId']; ?>">
                    </div>
                </div>
            </div>
            <div class="col-md-4 well pre-scrollable">
                <h4>Suggestions:</h4>
                <div class="well-sm row bg-info">
                    <div class="col-md-8">
                        <img class="thumbnail-scrollbar" src="assets/images/channelPic.png">
                    </div>
                    <div class="col-md-4 text-left no-padding suggestions-video-text">
                        <label>Video Title 1</label>
                        <p>Uploader 1</p>
                    </div>
                </div>

                <div class="well-sm row bg-info">
                    <div class="col-md-8">
                        <img class="thumbnail-scrollbar" src="assets/images/channelPic.png">
                    </div>
                    <div class="col-md-4 text-left no-padding suggestions-video-text">
                        <label>Video Title 2</label>
                        <p>Uploader 2</p>
                    </div>
                </div>

                <div class="well-sm row bg-info">
                    <div class="col-md-8">
                        <img class="thumbnail-scrollbar" src="assets/images/channelPic.png">
                    </div>
                    <div class="col-md-4 text-left no-padding suggestions-video-text">
                        <label>Video Title 3</label>
                        <p>Uploader 3</p>
                    </div>
                </div>

                <div class="well-sm row bg-info">
                    <div class="col-md-8">
                        <img class="thumbnail-scrollbar" src="assets/images/channelPic.png">
                    </div>
                    <div class="col-md-4 text-left no-padding suggestions-video-text">
                        <label>Video Title 4</label>
                        <p>Uploader 4</p>
                    </div>
                </div>

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

                        echo "<div class='row bg-info margin-5 width-100'>
                                    <div class='col-md-9'>
                                        <label>$username</label>
                                        <div class='well-sm''>
                                           <p>$commentText</p>
                                           <p class='date_style'>$dateAdded</p>
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