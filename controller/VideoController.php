<?php

namespace controller;

use model\Comment;
use model\db\CommentDao;
use model\db\TagDao;
use model\db\UserDao;
use model\User;
use model\db\VideoDao;

class VideoController extends BaseController {

    public function __construct() {
    }

    public function upload() {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        if ($requestMethod == 'GET') {
            $tagDao = TagDao::getInstance();
            $tags = $tagDao->getAll();

            $this->render('video/upload', [
                'tags' => $tags
            ]);
        }
    }
    
    public function watchAction() {

        $videoId = $_GET['id'];
        $videoDao = VideoDao::getInstance();

        $video = $videoDao->getByID($videoId);
        $videoUrl = $video->getVideoURL();
        $videoTitle = $video->getTitle();
        $videoDescription = $video->getDescription();
        $dateAdded = $video->getDateAdded();
        $uploaderId = $video->getUploaderID();
        $userDao = UserDao::getInstance();
        $uploader = $userDao->getById($uploaderId)->getUsername();

        $likes = $videoDao->getLikesCountById($videoId);
        $dislikes = $videoDao->getDislikesCountById($videoId);

        $commentDao = CommentDao::getInstance();
        $comments = $commentDao->getByVideoId($videoId);

        $logged = 'false';
        $loggedUserId = null;

        if (isset($_SESSION['user'])) {
            /* @var $loggedUser User */
            $loggedUser = $_SESSION['user'];
            $loggedUserId = $loggedUser->getId();
            $logged = 'true';
        }

        $this->render('video/watch', [
            'videoId' => $videoId,
            'videoUrl' => $videoUrl,
            'videoTitle' => $videoTitle,
            'videoDescription' => $videoDescription,
            'dateAdded' => $dateAdded,
            'uploader' => $uploader,
            'likes' => $likes,
            'dislikes' => $dislikes,
            'loggedUserId' => $loggedUserId,
            'logged' => $logged,
            'comments' => $comments
        ]);
    }

    public function likeDislikeVideoAction() {
        $videoId = $_GET['video-id'];
        $userId = $_GET['user-id'];
        $likeDislike = $_GET['like'];
        $videoDao = VideoDao::getInstance();
        if ($likeDislike == '1') {
            $videoDao->like($videoId, $userId);
        } else {
            $videoDao->dislike($videoId, $userId);
        }

        $likes = $videoDao->getLikesCountById($videoId);
        $dislikes = $videoDao->getDislikesCountById($videoId);

        $this->jsonEncodeParams([
            'likes' => $likes,
            'dislikes' => $dislikes
        ]);
    }

    public function likeDislikeCommentAction() {
        $commentId = $_GET['comment-id'];
        $userId = $_GET['user-id'];
        $likeDislike = $_GET['like'];
        $commentDao = CommentDao::getInstance();
        if ($likeDislike == '1') {
            $commentDao->like($commentId, $userId);
        } else {
            $commentDao->dislike($commentId, $userId);
        }

        $likes = $commentDao->getLikesCount($commentId);
        $dislikes = $commentDao->getDislikesCount($commentId);

        $this->jsonEncodeParams([
            'likes' => $likes,
            'dislikes' => $dislikes
        ]);
    }

    public function comment() {
        $commentText = $_POST['comment'];
        $videoId = $_POST['videoId'];
        $userId = $_POST['userId'];
        $date = date('Y-m-d');
        $comment = new Comment($videoId, $userId, $commentText, $date);

        $commentDao = CommentDao::getInstance();
        $commentDao->add($comment);

        /* @var $user User */
        $user = $_SESSION['user'];
        $username = $user->getUsername();

        $this->jsonEncodeParams([
            'comment' => $commentText,
            'date' => $date,
            'username' => $username
        ]);
    }
}
