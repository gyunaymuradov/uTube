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
        try {
            $videoDao = VideoDao::getInstance();

            $totalLikedVideosCount = $videoDao->getTotalLikedCount()['total_liked_count'];
            $mostLikedPagesCount = ceil($totalLikedVideosCount / 4);
            $mostLikedVideos = $videoDao->getMostLiked(4, 0);

            $videosCount = $videoDao->getTotalCount()['total_count'];
            $newestPagesCount = ceil($videosCount / 4);
            $newestVideos = $videoDao->getNewest(4, 0);

            $this->render('index/index', [
                'most_liked' => $mostLikedVideos,
                'most_liked_pages_count' => $mostLikedPagesCount,
                'newest' => $newestVideos,
                'newest_pages_count' => $newestPagesCount
            ]);
        }
        catch (\Exception $e) {
            $this->render('index/error');
        }
    }

    public function searchAction() {

        try {
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
                    } else if ($searchOption === 'user') {
                        $type = 'user';
                        $result = $userDao->search($value);
                    } else {
                        $type = 'playlist';
                        $result = $playlistDao->searchByName($value);
                    }
                }
                $this->render('index/search', [
                    'type' => $type,
                    'result' => $result
                ]);
            } else {
                $searchOption = $_GET['search-option'];
                if (is_null($searchOption)) {
                    header('Location:index.php');
                }
                $searchValue = $_GET['value'];

                if ($searchOption == 'video') {
                    $suggestions = $videoDao->getNameSuggestions($searchValue);
                } else if ($searchOption == 'user') {
                    $suggestions = $userDao->getSuggestionsByUsername($searchValue);
                } else {
                    $suggestions = $playlistDao->getNameSuggestions($searchValue);
                }

                $this->jsonEncodeParams([
                    'suggestions' => $suggestions
                ]);
            }
        }
        catch (\Exception $e) {
            $this->render('index/error');
        }
    }

    public function showError() {
        $this->render('index/error');
    }

    public function loadVideosAction() {

        $page = isset($_GET['pg']) ? $_GET['pg'] : 1;
        $videoDao = VideoDao::getInstance();
        if ($_GET['row'] == '1') {
            $videosCount = $videoDao->getTotalLikedCount()['total_liked_count'];
            $pagesCount = ceil($videosCount / 4);
            if ($page > $pagesCount) {
                $page = $pagesCount;
            } elseif  ($page <= 0) {
                $page = 1;
            }
            $offset = $page * 4 - 4;
            $videos = $videoDao->getMostLiked(4, $offset);
            $this->renderPartial('index/most-liked', [
                'most_liked' => $videos
            ]);
        } else {
            $videosCount = $videoDao->getTotalCount()['total_count'];
            $pagesCount = ceil($videosCount / 4);
            if ($page > $pagesCount) {
                $page = $pagesCount;
            } elseif  ($page <= 0) {
                $page = 1;
            }
            $offset = $page * 4 - 4;
            $videos = $videoDao->getNewest(4, $offset);
            $this->renderPartial('index/newest', [
                'newest' => $videos
            ]);
        }
    }
}


