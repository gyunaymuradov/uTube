<?php

use model\db\CommentDao;
use model\db\UserDao;
use model\db\VideoDao;
use model\Video;
use model\Comment;
use model\User;

function __autoload($className) {
       $className = '..\\' . $className;
       $className = str_replace("\\", "/", $className);
       require_once $className . '.php';
    }


    $videoModel = VideoDao::getInstance();
    $userModel = UserDao::getInstance();
    $commentModel = CommentDao::getInstance();


    $video = $videoModel->getOneVideo(5);
    $title = $video->getTitle();
    $description = $video->getDescription();
    $src = '../' . $video->getVideoURL();
    $dateAdded = $video->getDateAdded();

    $uploaderId = $video->getUploaderID();
    $uploader = $userModel->getUserById($uploaderId);
    $uploaderUsername = $uploader->getUsername();
    $uploaderFirstName = $uploader->getFirstName();
    $uploaderLastName = $uploader->getLastName();
    $uploaderEmail = $uploader->getEmail();

    /* @var $comments Comment[] */
    $comments = $commentModel->getCommentsByVideoId(5);

?>


<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>uTube</title>
    </head>
    <body>
    <video width="600" height="500" controls autoplay>
        <source src="<?= $src ?>" type="video/mp4">
    </video>

    <div><?= $description ?></div>
    <div><?= 'Added on ' . $dateAdded ?></div>
    <div><?= 'Uploaded by ' . $uploaderUsername ?></div>
    <ul>
        <?php

        foreach ($comments as $comment) {
            $commentator = $comment->getCreatorUsername();
            $text = $comment->getText();
            $dateAsString = $comment->getDateAdded();
            $dateAdded = strtotime($dateAsString);
            $dateAdded = date('d-M-y', $dateAdded);
            echo "<li><strong>$commentator</strong></li>";
            echo "<li>$text</li>";
            echo "<li>$dateAdded</li><br>";
        }

        ?>
    </ul>
    </body>
</html>