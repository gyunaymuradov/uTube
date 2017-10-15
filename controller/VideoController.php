<?php

namespace controller;

use model\db\UserDao;
use model\User;
use model\db\VideoDao;

class VideoController extends BaseController {

    public function __construct() {
    }

    public function uploadFormAction() {
        $this->render('video/upload');
    }

    public function upload() {
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
        ]);
    }

    public function likeDislikeAction() {
        $videoId = $_GET['video-id'];
        $userId = $_GET['user-id'];
        $likeDislike = $_GET['like'];
        $result = '';
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
}
