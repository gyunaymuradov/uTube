<?php

namespace controller;

use model\db\PlaylistDao;
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

        $videoDao = VideoDao::getInstance();
        $userDao = UserDao::getInstance();
        $playlistDao = PlaylistDao::getInstance();

        if (isset($_POST['search'])) {
            $searchOption = $_POST['search-option'];
            $value = $_POST['value'];
            $type = 'video';
            $result = array();
            if (strlen(trim($value)) != 0) {
                if ($searchOption === 'video') {
                    $result = $videoDao->searchByName($value);
                } else {
                    $type = 'user';
                    $result = $userDao->search($value);
                }
            }
            $this->render('index/search', [
                'type' => $type,
                'result' => $result
            ]);
        } else {
            $searchOption = $_GET['search-option'];
            $searchValue = $_GET['value'];

            if ($searchOption == 'video') {
                $suggestions = $videoDao->getNameSuggestions($searchValue);
            } else if ($searchOption == 'user') {
                $suggestions = $userDao->getSuggestionsByUsername($searchValue);
            } else {
                $suggestions = $playlistDao->getNameSuggestions($searchValue);
                // TODO GET THE PLAYLISTS AND DISPLAY THEM IN THE SEARCH PAGE
            }

            $this->jsonEncodeParams([
                'suggestions' => $suggestions
            ]);
        }
    }
}


