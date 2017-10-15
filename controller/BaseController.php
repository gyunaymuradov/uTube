<?php

namespace controller;

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

        $params['userPhotoSrc'] = $userPhotoSrc;
        $params['userId'] = $userId;

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

}
