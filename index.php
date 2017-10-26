<?php

session_start();

function __autoload($className) {
    $className = str_replace("\\", "/", $className);
    require_once $className . '.php';
}

    $controller = isset($_GET['page']) ? $_GET['page'] : null;
    $restrictedPages = ['profile',
                        'edit-profile',
                        'subscribe',
                        'logout',
                        'like-video',
                        'like-comment',
                        'delete-video',
                        'edit-video',
                        'upload',
                        'comment',
                        'get-playlist-names',
                        'playlist-create',
                        'playlist-rename',
                        'playlist-delete'
                        ];

    if (!isset($_SESSION['user']) && in_array($controller, $restrictedPages)) {
        $controller = new controller\UserController();
        $controller->login();
    }
    else {
        $controllerName = isset($_GET['page']) ? $_GET['page'] : 'index';
        $action = isset($_GET['action']) ? $_GET['action'] : 'index';
        $className = '\\controller\\' . ucfirst($controllerName) . 'Controller';
        if (class_exists($className)) {
            $controller = new $className;
            if (method_exists($controller, $action)) {
                $controller->$action();
            } else {
                $controller = new controller\IndexController();
                $controller->index();
            }
        } else {
            $controller = new controller\IndexController();
            $controller->index();
        }

//            if ($page === 'profile' && !empty($_GET['id'])) {
//                $controller = new controller\UserController();
//                $controller->viewProfileAction();
//            } else if ($page === 'user') {
//                $controller = new controller\UserController();
//                $controller->viewUserAction();
//            } else if ($page === 'edit-profile') {
//                $controller = new controller\UserController();
//                $controller->editProfileAction();
//            } else if ($page === 'load-videos') {
//                $controller = new controller\UserController();
//                $controller->getVideosAction();
//            } else if ($page === 'index-videos') {
//                $controller = new controller\IndexController();
//                $controller->loadVideosAction();
//            } else if ($page === 'load-playlists') {
//                $controller = new controller\UserController();
//                $controller->getPlaylistsAction();
//            } else if ($page === 'subscribe') {
//                $controller = new controller\UserController();
//                $controller->subscribeAction();
//            } else if ($page === 'login') {
//                $controller = new controller\UserController();
//                $controller->loginAction();
//            } else if ($page === 'about') {
//                $controller = new controller\UserController();
//                $controller->getAboutPage();
//            } else if ($page === 'register') {
//                $controller = new controller\UserController();
//                $controller->registerAction();
//            } else if ($page === 'logout') {
//                $controller = new controller\UserController();
//                $controller->logoutAction();
//            } else if ($page === 'like-video') {
//                $controller = new controller\VideoController();
//                $controller->likeDislikeVideoAction();
//            } else if ($page === 'like-comment') {
//                $controller = new controller\VideoController();
//                $controller->likeDislikeCommentAction();
//            } else if ($page === 'watch') {
//                $controller = new controller\VideoController();
//                // check in controller if there is record with this id
//                $controller->watchAction();
//            } else if ($page === 'delete-video') {
//                $controller = new controller\VideoController();
//                $controller->deleteAction();
//            } else if ($page === 'edit-video') {
//                $controller = new controller\VideoController();
//                $controller->editAction();
//            } else if ($page === 'upload') {
//                $controller = new controller\VideoController();
//                $controller->uploadAction();
//            } else if ($page === 'comment') {
//                $controller = new controller\VideoController();
//                $controller->commentAction();
//            } else if ($page === 'search') {
//                $controller = new controller\IndexController();
//                $controller->searchAction();
//            } else if ($page === 'get-playlist-names') {
//                $controller = new controller\PlaylistController();
//                $controller->getNames();
//            } else if ($page === 'playlist-create') {
//                $controller = new controller\PlaylistController();
//                $controller->createPlaylist();
//            } else if ($page === 'playlist-insert') {
//                $controller = new controller\PlaylistController();
//                $controller->insertVideo();
//            } else if ($page === 'playlist-rename') {
//                $controller = new controller\PlaylistController();
//                $controller->renamePlaylist();
//            } else if ($page === 'get-playlist-videos') {
//                $controller = new controller\PlaylistController();
//                $controller->getVideos();
//            } else if ($page === 'playlist-delete') {
//                $controller = new controller\PlaylistController();
//                $controller->removeVideo();
//            } else if ($page === 'delete-playlist') {
//                $controller = new controller\PlaylistController();
//                $controller->deletePlaylist();
//            } else if ($page === 'error') {
//                $controller = new controller\IndexController();
//                $controller->showError();
//            } else {
//                $controller = new controller\IndexController();
//                $controller->indexAction();
//            }
    }