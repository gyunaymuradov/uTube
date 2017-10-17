<?php

namespace controller;

use model\db\UserDao;
use model\db\VideoDao;
use \model\User;

class BaseController {

    public function __construct() {

    }

    public function render($file, $params = []) {
        $userPhotoSrc = null;
        $userId = null;
        $logged = false;
        if (isset($_SESSION['user'])) {
            /* @var $user User */
            $user = $_SESSION['user'];
            $userPhotoSrc = /* '/uTube/root/' . */ $user->getUserPhotoUrl();
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
        $params['userPhotoSrc'] = $userPhotoSrc;
        $params['userId'] = $userId;
        $params['navSuggestions'] = $suggestions;
        $params['navTitle'] = $navTitle;

        require_once '../view/header.php';
        require_once '../view/nav.php';
        require_once '../view/' . $file . '.php';
        require_once '../view/footer.php';
    }

    public function renderPartial($file, $params = []) {
        require_once '../view/' . $file . '.php';
    }

    public function jsonEncodeParams($params = []) {
        echo json_encode($params);
    }

    // " SELECT u.username, u.id FROM users u JOIN follows f ON  u.id = f.followed_id GROUP BY followed_id ORDER BY COUNT(f.follower_id) DESC LIMIT 10
    // SELECT COUNT(*) as followed_count FROM users u JOIN follows f ON u.id = f.followed_id WHERE f.follower_id = ?"

}
