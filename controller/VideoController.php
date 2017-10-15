<?php

namespace controller;

use model\db\UserDao;
use model\db\VideoDao;

class VideoController extends BaseController {

    public function __construct() {
    }

    public function uploadFormAction() {
        $this->render('video/upload');
    }

    public function upload() {
        // TODO add the upload logic
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

        $this->render('video/watch', [
            'videoUrl' => $videoUrl,
            'videoTitle' => $videoTitle,
            'videoDescription' => $videoDescription,
            'dateAdded' => $dateAdded,
            'uploader' => $uploader
        ]);
    }

}
