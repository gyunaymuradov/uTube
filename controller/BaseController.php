<?php

namespace controller;

use model\db\UserDao;
use model\db\VideoDao;
use \model\User;

class BaseController {

    public function __construct() {

    }

//    private function escapeParameters($params) {
//        if(is_array($params)) {
//            $result = array();
//            foreach($params as $key=>$param) {
//                $result[$key] = $this->escapeParameters($param);
//            }
//            return $result;
//        } else {
//            return htmlentities($params);
//        }
//    }

    public function render($file, $params = []) {
//        $params = $this->escapeParameters($params);
        $userPhotoSrc = null;
        $userId = null;
        $logged = false;
        if (isset($_SESSION['user'])) {
            /* @var $user User */
            $user = $_SESSION['user'];
            $userPhotoSrc = $user->getUserPhotoUrl();
            $userId = $user->getId();
            $logged = true;
        }

        $navTitle = 'Subscriptions:';

        $userDao = UserDao::getInstance();
        $suggestions = $userDao->getSubscriptions($userId);
        if (count($suggestions) == 0) {
            $navTitle = 'Most subscribed users:';
            $suggestions = $userDao->getMostSubscribed();

        }
        $params['user_photo_src'] = $userPhotoSrc;
        $params['user_id'] = $userId;
        $params['nav_suggestions'] = $suggestions;
        $params['nav_title'] = $navTitle;
        $params['search_placeholder'] = 'Search video';

        require_once 'view/header.php';
        require_once 'view/nav.php';
        require_once 'view/' . $file . '.php';
        require_once 'view/footer.php';
    }

    public function renderPartial($file, $params = []) {
//        $params = $this->escapeParameters($params);
        require_once 'view/' . $file . '.php';
    }

    public function jsonEncodeParams($params = []) {
//        $params = $this->escapeParameters($params);
        echo json_encode($params, JSON_UNESCAPED_SLASHES);
    }
}
