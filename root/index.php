<?php

session_start();

function __autoload($className) {
    $className = '..\\' . $className;
    $className = str_replace("\\", "/", $className);
    require_once $className . '.php';
}

    $page = isset($_GET['page']) ? $_GET['page'] : null;
//when url is ....?controller=XXX&action=YYY
//invoke XXXController.php and start YYY() in it
//ex: localhost:8080/MyYoutube?controller=user&action=login
//new UserController()->login();
//$controller - "";
//$action = "";
//$className = "../controller/".ucfirst($controller).'Controller';
//(new $className())->$action();
    
    if($page === 'profile') {
        $controller = new controller\UserController();
        $controller->viewProfileAction();
    }
    else if($page === 'user') {
        $controller = new controller\UserController();
        $controller->viewUserAction();
    }
    else if($page === 'edit-profile') {
        $controller = new controller\UserController();
        $controller->editProfileAction();
    }
    else if($page === 'subscribe') {
        $controller = new controller\UserController();
        $controller->subscribeAction();
    }
    else if($page === 'login-register') {
        $controller = new controller\UserController();
        $controller->loginRegisterAction();
    }
    else if($page === 'login') {
        $controller = new controller\UserController();
        $controller->loginAction();
    }
    else if($page === 'about') {
        $controller = new controller\UserController();
        $controller->getAboutPage();
    }
    else if($page === 'register') {
        $controller = new controller\UserController();
        $controller->registerAction();
    }
    else if($page === 'register-success') {
        $controller = new controller\UserController();
        $controller->registerSuccess();
    }
    else if($page === 'logout') {
        $controller = new controller\UserController();
        $controller->logoutAction();
    }
    else if($page === 'like-video') {
        $controller = new controller\VideoController();
        $controller->likeDislikeVideoAction();
    }
    else if($page === 'like-comment') {
        $controller = new controller\VideoController();
        $controller->likeDislikeCommentAction();
    }
    else if($page === 'watch') {
        $controller = new controller\VideoController();
        $controller->watchAction();
    }
    else if($page === 'delete-video') {
        $controller = new controller\VideoController();
        $controller->deleteAction();
    }
    else if($page === 'edit-video') {
        $controller = new controller\VideoController();
        $controller->editAction();
    }
    else if($page === 'upload') {
        $controller = new controller\VideoController();
        $controller->upload();
    }
    else if($page === 'comment') {
        $controller = new controller\VideoController();
        $controller->comment();
    }
    else if($page === 'search') {
        $controller = new controller\IndexController();
        $controller->searchAction();
    }
    else if($page === 'get-playlist-names') {
        $controller = new controller\PlaylistController();
        $controller->getNames();
    }
    else if($page === 'playlist-create') {
        $controller = new controller\PlaylistController();
        $controller->createPlaylist();
    }
    else if($page === 'playlist-insert') {
        $controller = new controller\PlaylistController();
        $controller->insertVideo();
    }
    else if($page === 'playlist-rename') {
        $controller = new controller\PlaylistController();
        $controller->renamePlaylist();
    }
    else if($page === 'get-playlist-videos') {
        $controller = new controller\PlaylistController();
        $controller->getVideos();
    }
    else if($page === 'playlist-delete') {
        $controller = new controller\PlaylistController();
        $controller->removeVideo();
    }
    else {
        $controller = new controller\IndexController();
        $controller->indexAction();
    }