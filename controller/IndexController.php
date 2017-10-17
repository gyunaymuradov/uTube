<?php

namespace controller;

use model\db\VideoDao;
use model\db\UserDao;
use model\User;

class IndexController extends BaseController {

    public function __construct() {

    }

    public function indexAction() {

        $videoDao = VideoDao::getInstance();
        $mostLikedVideos = $videoDao->getMostLiked();
        $newestVideos = $videoDao->getNewest();

        $this->render('index/index', [
            'mostLiked' => $mostLikedVideos,
            'newest' => $newestVideos
        ]);
    }

    public function searchAction() {

        if (isset($_POST['search'])) {
            $searchOption = $_POST['search-option'];
            $value = $_POST['value'];
            $type = 'video';
            $result = array();
            if ($searchOption === 'video') {
                $videoDao = VideoDao::getInstance();
                $result = $videoDao->searchByName($value);
            } else {
                $type = 'user';
                $userDao = UserDao::getInstance();
                $result = $userDao->search($value);
            }

            $this->render('index/search', [
                'type' => $type,
                'result' => $result
            ]);
        }
    }
}


