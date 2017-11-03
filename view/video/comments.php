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
                                            <img src='$userPhoto' class='img-circle margin-5' width='50' height='auto'>&nbsp;&nbsp;<label class='margin-5'><a href='index.php?controller=user&action=user&id=$userId'>$username</a></label>
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
