<?php

namespace controller;

use model\db\UserDao;
use model\db\VideoDao;
use model\db\PlaylistDao;
use model\User;


class UserController extends BaseController {

    public function __construct() {

    }

    public function register()
    {
        try {
            $requestMethod = $_SERVER['REQUEST_METHOD'];

            if ($requestMethod == 'GET') {
                $username = '';
                $firstName = '';
                $lastName = '';
                $email = '';
                $errors = '';
                $this->renderPartial('user/register', [
                    'errors' => $errors,
                    'username' => $username,
                    'first-name' => $firstName,
                    'last-name' => $lastName,
                    'email' => $email
                ]);
            } else if ($requestMethod == 'POST'
                && isset($_POST['username'])
                && isset($_POST['first-name'])
                && isset($_POST['last-name'])
                && isset($_POST['email'])
                && isset($_POST['password'])
                && isset($_POST['confirm-pass'])
                && isset($_POST['register'])) {
                $username = $_POST['username'];
                $firstName = $_POST['first-name'];
                $lastName = $_POST['last-name'];
                $email = $_POST['email'];
                $pass = $_POST['password'];
                $confirmPass = $_POST['confirm-pass'];

                $validator = Validator::getInstance();
                $validUsername = $validator->validateUsername($username);
                $validFirstName = $validator->validateFirstName($firstName);
                $validLastName = $validator->validateLastName($lastName);
                $validEmail = $validator->validateEmail($email);
                $validPassword = $validator->validatePassword($pass, $confirmPass);


                $errors = array();
                if (is_array($validUsername)) {
                    foreach ($validUsername as $error) {
                        $errors['username'][] = $error;
                    }
                }
                if (is_array($validEmail)) {
                    foreach ($validEmail as $error) {
                        $errors['email'][] = $error;
                    }
                }
                if (is_array($validFirstName)) {
                    foreach ($validFirstName as $error) {
                        $errors['first_name'][] = $error;
                    }
                }
                if (is_array($validLastName)) {
                    foreach ($validLastName as $error) {
                        $errors['last_name'][] = $error;
                    }
                }
                if (is_array($validPassword)) {
                    if (isset($validPassword['confirm-pass'])) {
                        $errors['confirm-pass'] = $validPassword['confirm-pass'];
                        unset($validPassword['confirm-pass']);
                    }
                    foreach ($validPassword as $error) {
                        $errors['password'][] = $error;
                    }
                }

                $hasUploadedImg = !empty($_FILES['photo']['name']) && $_FILES['photo']['size'] != 0;
                if ($hasUploadedImg) {
                    $fileRealName = $_FILES['photo']['name'];
                    $fileTempName = $_FILES['photo']['tmp_name'];
                    $extensions = ['jpeg', 'jpg', 'png'];
                    $validImg = $validator->validateUploadedFile($fileRealName, $fileTempName, 5000000, 'image', $extensions);
                    if (is_array($validImg)) {
                        foreach ($validImg as $error) {
                            $errors['img'][] = $error;
                        }
                    }
                }

                $userDao = UserDao::getInstance();
                $usernameTaken = $userDao->checkIfExists($username);

                if ($usernameTaken) {
                    $errors['username'][] = 'Username is already taken.';
                }
                if (empty($errors)) {
                    $user = new User();
                    $user->setUsername($username);
                    $user->setPassword(password_hash($pass, PASSWORD_DEFAULT));
                    $user->setEmail($email);
                    $user->setFirstName($firstName);
                    $user->setLastName($lastName);
                    $user->setDateJoined(date('Y-m-d'));
                    if ($hasUploadedImg) {
                        if (!file_exists('../uploads/user_photos')) {
                            mkdir('../uploads/user_photos', 0777);
                        }
                        $realFileName = $_FILES['photo']['name'];
                        $imgName = 'IMG_' . time();
                        $imgPath = "uploads/user_photos/$imgName." . pathinfo($realFileName, PATHINFO_EXTENSION);
                        move_uploaded_file($_FILES['photo']['tmp_name'], $imgPath);
                        $user->setUserPhotoUrl($imgPath);
                    } else {
                        $user->setUserPhotoUrl('uploads/default_photo.png');
                    }
                    $success = $userDao->insert($user);
                    if ($success) {
                        $user->setPassword($pass);
                        $result = $userDao->login($user);
                        if ($result) {
                            $_SESSION['user'] = $result;
                            header("Location:index.php");
                        } else {
                            $this->render('index/error');
                        }
                    }
                } else {
                    $this->renderPartial('user/register', [
                        'errors' => $errors,
                        'username' => $username,
                        'first-name' => $firstName,
                        'last-name' => $lastName,
                        'email' => $email
                    ]);
                }
                }
            }
        catch
            (\Exception $e) {
                $this->render('index/error');
            }
    }

    public function login() {
        try {
            $requestMethod = $_SERVER['REQUEST_METHOD'];
            if ($requestMethod == 'GET') {
                $errors = '';
                $username = '';
                $this->renderPartial('user/login', [
                    'errors' => $errors,
                    'username' => $username,
                ]);
            } else if ($requestMethod == 'POST'
                && isset($_POST['username'])
                && isset($_POST['password'])) {

                $username = $_POST['username'];
                $password = $_POST['password'];

                $userDao = UserDao::getInstance();
                $user = new User();
                $user->setUsername($username);
                $user->setPassword($password);
                $result = $userDao->login($user);
//            This checks the encrypted password. Uncomment when done with profile edit.
                if ($result) {
                    $_SESSION['user'] = $result;
                    header("Location:index.php");
                } else {
                    $errors = 'Invalid username or password.';
                    $this->renderPartial('user/login', [
                        'errors' => $errors,
                        'username' => $username,
                    ]);
                }
            }
        }
        catch (\Exception $e) {
            $this->render('index/error');
        }
    }

    public function logout() {
        session_start();
        session_destroy();
        header('Location:index.php');
    }

    public function subscribe() {
        try {
            $loggedUserId = $_GET['loggedId'];
            $profileId = $_GET['profileId'];

            /* @var $userDao UserDao */
            $userDao = UserDao::getInstance();
            $alreadyFollowed = $userDao->checkIfFollowed($profileId, $loggedUserId);
            if (!$alreadyFollowed) {
                $userDao->follow($loggedUserId, $profileId);
            } else {
                $userDao->unfollow($loggedUserId, $profileId);
            }

            $suggestions = $userDao->getSubscriptions($loggedUserId);
            if (count($suggestions) == 0) {
                $navTitle = 'Most subscribed users:';
                $suggestions = $userDao->getMostSubscribed();
            } else {
                $navTitle = 'Subscriptions:';
            }
            $this->renderPartial('index/subscribes', [
                'nav_title' => $navTitle,
                'nav_suggestions' => $suggestions
            ]);
        }
        catch (\Exception $e) {
            $this->render('index/error');
        }
    }

    public function user() {
        try {
            $profileId = $_GET['id'];
            $logged = 'false';
            $loggedUserId = null;
            if (isset($_SESSION['user'])) {
                $logged = 'true';
                /* @var $loggedUser User */
                $loggedUser = $_SESSION['user'];
                $loggedUserId = $loggedUser->getId();
            }

            if ($profileId == $loggedUserId) {
                header('Location:index.php?page=user&action=profile&id=' . $loggedUserId);
            }

            /* @var $userDao \model\db\UserDao */
            $userDao = UserDao::getInstance();
            $user = $userDao->getById($profileId);
            if (is_null($user)) {
                header('Location:index.php');
            }
            $subscribeButtonText = 'Subscribe';
            $alreadySubscribed = $userDao->checkIfFollowed($profileId, $loggedUserId);
            if ($alreadySubscribed) {
                $subscribeButtonText = 'Unsubscribe';
            }

            $userPhoto = $user->getUserPhotoUrl();
            $firstName = $user->getFirstName();
            $lastName = $user->getLastName();
            $username = $user->getUsername();
            $email = $user->getEmail();
            $dateJoined = $user->getDateJoined();


            /* @var $videoDao VideoDao */
            $videoDao = VideoDao::getInstance();

            $videos = $videoDao->getNLatestByUploaderID($profileId, 4, 0);
            $videosCount = $videoDao->getCountByUploaderId($profileId)['video_count'];
            $videoPagesCount = ceil($videosCount / 4);
            $videoBtnsVisibility = 'block';
            if ($videoPagesCount < 2) {
                $videoBtnsVisibility = 'none';
            }

            /* @var $playlistDao PlaylistDao */
            $playlistDao = PlaylistDao::getInstance();

            $playlists = $playlistDao->getNLatestByCreatorID($profileId, 4, 0);
            $playlistsCount = $playlistDao->getCountByCreatorId($profileId)['playlist_count'];
            $playlistPagesCount = ceil($playlistsCount / 4);
            $playlistBtnsVisibility = 'block';
            if ($playlistPagesCount < 2) {
                $playlistBtnsVisibility = 'none';
            }

            $subscribersCount = $userDao->getSubscribersCount($profileId);
            $subscriptionsCount = $userDao->getSubscriptionsCount($profileId);

            $this->render('user/user', [
                'userPhoto' => $userPhoto,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'username' => $username,
                'profileId' => $profileId,
                'email' => $email,
                'dateJoined' => $dateJoined,
                'subscribersCount' => $subscribersCount,
                'subscriptionsCount' => $subscriptionsCount,
                'logged' => $logged,
                'videos' => $videos,
                'playlists' => $playlists,
                'playlist_pages_count' => $playlistPagesCount,
                'playlist_btns_vsblty' => $playlistBtnsVisibility,
                'loggedUserId' => $loggedUserId,
                'subscribeButton' => $subscribeButtonText,
                'video_pages_count' => $videoPagesCount,
                'video_btns_vsblty' => $videoBtnsVisibility
            ]);
        }
        catch (\Exception $e) {
            $this->render('index/error');
        }
    }

    public function edit()
    {
        try {
            $userDao = UserDao::getInstance();
            $requestMethod = $_SERVER['REQUEST_METHOD'];
            if ($requestMethod == 'GET') {
                $user = new User();
                $userId = $_GET['id'];
                /* @var $user User */
                $user = $userDao->getById($userId);
                $username = $user->getUsername();
                $firstName = $user->getFirstName();
                $lastName = $user->getLastName();
                $email = $user->getEmail();

                $this->renderPartial('user/edit-profile', [
                    'user-id' => $userId,
                    'username' => $username,
                    'first-name' => $firstName,
                    'last-name' => $lastName,
                    'email' => $email,
                    'msg' => ''
                ]);
            } else if ($requestMethod == 'POST') {
                $userId = $_POST['user_id'];
                $username = $_POST['username'];
                $firstName = $_POST['first_name'];
                $lastName = $_POST['last_name'];
                $email = $_POST['email'];
                $oldPass = $_POST['old_pass'];
                $newPass = $_POST['new_pass'];
                $newPassConfirm = $_POST['new_pass_confirm'];
                $oldUsername = $_SESSION['user']->getUsername();
                $msg = null;
                $validator = Validator::getInstance();
                $validUsername = $validator->validateUsername($username);
                $validFirstName = $validator->validateFirstName($firstName);
                $validLastName = $validator->validateLastName($lastName);
                $validEmail = $validator->validateEmail($email);

                $errors = array();
                if (is_array($validUsername)) {
                    foreach ($validUsername as $error) {
                        $errors['username'][] = $error;
                    }
                }
                if (is_array($validEmail)) {
                    foreach ($validEmail as $error) {
                        $errors['email'][] = $error;
                    }
                }
                if (is_array($validFirstName)) {
                    foreach ($validFirstName as $error) {
                        $errors['first_name'][] = $error;
                    }
                }
                if (is_array($validLastName)) {
                    foreach ($validLastName as $error) {
                        $errors['last_name'][] = $error;
                    }
                }

                if ($oldUsername != $username) {
                    $usernameTaken = $userDao->checkIfExists($username);
                    if ($usernameTaken) {
                        $errors['username'][] = 'Username is already taken.';
                    }
                }

                if (strlen($newPass) > 0) {
                    $validNewPass = $validator->validatePassword($newPass, $newPassConfirm);
                    if (is_array($validNewPass)) {
                        if (isset($validNewPass['confirm-pass'])) {
                            $errors['confirm-pass'] = $validNewPass['confirm-pass'];
                            unset($validNewPass['confirm-pass']);
                        }
                        foreach ($validNewPass as $error) {
                            $errors['password'][] = $error;
                        }
                    }
                }

                if (empty($errors) && strlen($newPass) == 0) {
                    $user = new User();
                    $user->setUsername($username);
                    $user->setId($userId);
                    $user->setEmail($email);
                    $user->setFirstName($firstName);
                    $user->setLastName($lastName);

                    $userDao->edit($user);
                    $_SESSION['user'] = $userDao->getInfo($userId);
                    http_response_code(304);
                } else if (empty($errors) && strlen($newPass) > 0) {
                    $user = new User();
                    $user->setUsername($username);
                    $user->setId($userId);
                    $user->setPassword(password_hash($newPass, PASSWORD_DEFAULT));
                    $user->setEmail($email);
                    $user->setFirstName($firstName);
                    $user->setLastName($lastName);
                    $rowsAffected = $userDao->editWithPass($user, $_SESSION['user']->getUsername(), $oldPass);
                    if ($rowsAffected == 0) {
                        $errors['old-password'] = 'Incorrect password';
                        $this->renderPartial('user/edit-profile', [
                            'errors' => $errors,
                            'user-id' => $userId,
                            'username' => $username,
                            'first-name' => $firstName,
                            'last-name' => $lastName,
                            'email' => $email
                        ]);
                    } else {
                        $_SESSION['user'] = $userDao->getInfo($userId);
                        http_response_code(304);
                    }
                } else {
                    $this->renderPartial('user/edit-profile', [
                        'errors' => $errors,
                        'user-id' => $userId,
                        'username' => $username,
                        'first-name' => $firstName,
                        'last-name' => $lastName,
                        'email' => $email,
                    ]);
                }
            }
        }
//            $hasUploadedImg = !empty($_FILES['photo']['name']) && $_FILES['photo']['size'] != 0;
//
//            if ($hasUploadedImg) {
//                $fileRealName = $_FILES['photo']['name'];
//                $fileTempName= $_FILES['photo']['tmp_name'];
//                $extensions = ['jpeg', 'jpg', 'png'];
//                $validImg = $validator->validateUploadedFile($fileRealName, $fileTempName, 5000000,  'image', $extensions);
//                if (is_array($validImg)) {
//                    foreach ($validImg as $error) {
//                        $errors['img'][] = $error;
//                    }
//                }
//            }
//            if (!empty($_FILES['photo']['name']) && $_FILES['photo']['size'] != 0) {
//                if (!file_exists("../uploads/user_photos")) {
//                    mkdir("../uploads/user_photos", 0777);
//                }
//                unlink($imgPath);
//                $realFileName = $_FILES['photo']['name'];
//                $imgName = 'IMG_' . time();
//                $imgPath = "../uploads/user_photos/$imgName." . pathinfo($realFileName, PATHINFO_EXTENSION);
//                move_uploaded_file($_FILES['photo']['tmp_name'], $imgPath);
//            }

        catch (\Exception $e) {
                $this->render('index/error');
            }
    }

    public function profile() {
        try {
            /* @var $userDao UserDao */
            $userDao = UserDao::getInstance();
            /* @var $videoDao VideoDao */
            $videoDao = VideoDao::getInstance();
            /* @var $playlistDao PlaylistDao */
            $playlistDao = PlaylistDao::getInstance();
            /* @var $user User */
            $user = $_SESSION['user'];

            $userPhoto = $user->getUserPhotoUrl();
            $firstName = $user->getFirstName();
            $lastName = $user->getLastName();
            $username = $user->getUsername();
            $userId = $user->getId();
            $email = $user->getEmail();
            $dateJoined = $user->getDateJoined();

            $videos = $videoDao->getNLatestByUploaderID($userId, 4, 0);
            $videosCount = $videoDao->getCountByUploaderId($userId)['video_count'];
            $videoPagesCount = ceil($videosCount / 4);
            $videoBtnsVisibility = 'block';
            if ($videoPagesCount < 2) {
                $videoBtnsVisibility = 'none';
            }

            $playlists = $playlistDao->getNLatestByCreatorID($userId, 4, 0);
            $playlistsCount = $playlistDao->getCountByCreatorId($userId)['playlist_count'];
            $playlistPagesCount = ceil($playlistsCount / 4);
            $playlistBtnsVisibility = 'block';
            if ($playlistPagesCount < 2) {
                $playlistBtnsVisibility = 'none';
            }

            $subscribersCount = $userDao->getSubscribersCount($userId);
            $subscriptionsCount = $userDao->getSubscriptionsCount($userId);

            $this->render('user/profile', [
                'userPhoto' => $userPhoto,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'username' => $username,
                'userId' => $userId,
                'email' => $email,
                'dateJoined' => $dateJoined,
                'subscribersCount' => $subscribersCount,
                'subscriptionsCount' => $subscriptionsCount,
                'videos' => $videos,
                'video_pages_count' => $videoPagesCount,
                'video_btns_vsblty' => $videoBtnsVisibility,
                'playlists' => $playlists,
                'playlist_pages_count' => $playlistPagesCount,
                'playlist_btns_vsblty' => $playlistBtnsVisibility,
            ]);
        }
        catch (\Exception $e) {
            $this->render('index/error');
        }
    }

    public function about() {
        try {
            $userId = $_GET['id'];
            $userDao = UserDao::getInstance();
            $userObj = $userDao->getById($userId);
            $userArr = array();
            $userArr['username'] = $userObj->getUsername();
            $userArr['userPhoto'] = $userObj->getUserPhotoUrl();
            $userArr['subscribers'] = $userDao->getSubscribersCount($userId);
            $userArr['first_name'] = $userObj->getFirstName();
            $userArr['last_name'] = $userObj->getLastName();
            $userArr['email'] = $userObj->getEmail();
            $userArr['date_joined'] = $userObj->getDateJoined();
            $userArr['subscriptions'] = $userDao->getSubscriptionsCount($userId);

            $this->jsonEncodeParams($userArr);
        }
        catch (\Exception $e) {
            $this->render('index/error');
        }
    }

    public function getVideos() {
        /* @var $videoDao VideoDao */
        $videoDao = VideoDao::getInstance();
        $userId = $_GET['id'];
        $page = isset($_GET['pg']) ? $_GET['pg'] : 1;

        $videosCount = $videoDao->getCountByUploaderId($userId)['video_count'];
        $pagesCount = ceil($videosCount / 4);

        if ($page > $pagesCount) {
            $page = $pagesCount;
        } elseif  ($page <= 0) {
            $page = 1;
        }

        $offset = $page * 4 - 4;

        /* @var $videos \model\Video */
        $videos = $videoDao->getNLatestByUploaderID($userId, 4, $offset);

        $this->renderPartial('user/videos', [
            'videos' => $videos,
        ]);
    }

    public function getPlaylists() {
        /* @var $playlistDao PlaylistDao */
        $playlistDao = PlaylistDao::getInstance();
        $userId = $_GET['id'];
        $page = isset($_GET['pg']) ? $_GET['pg'] : 1;

        $playlistsCount = $playlistDao->getCountByCreatorId($userId)['playlist_count'];
        $playlisPagesCount = ceil($playlistsCount / 4);

        if ($page > $playlisPagesCount) {
            $page = $playlisPagesCount;
        } elseif  ($page <= 0) {
            $page = 1;
        }

        $offset = $page * 4 - 4;

        /* @var $videos \model\Video */
        $playlists = $playlistDao->getNLatestByCreatorID($userId, 4, $offset);

        $this->renderPartial('user/playlists', [
            'playlists' => $playlists,
        ]);
    }

}
