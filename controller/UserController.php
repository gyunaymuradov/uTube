<?php

namespace controller;

use model\db\UserDao;
use model\db\VideoDao;
use model\db\PlaylistDao;
use model\User;


class UserController extends BaseController {

    public function __construct() {

    }

    public function registerAction()
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

    public function loginAction() {
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

    public function logoutAction() {
        session_start();
        session_destroy();
        header('Location:index.php');
    }

    public function subscribeAction() {
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

    public function viewUserAction() {
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
                header('Location:index.php?page=profile&id=' . $loggedUserId);
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

            $videos = $videoDao->getNLatestByUploaderID(10, $profileId);

            /* @var $playlistDao PlaylistDao */
            $playlistDao = PlaylistDao::getInstance();

            $playlists = $playlistDao->getNLatestByCreatorID(10, $profileId);

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
                'loggedUserId' => $loggedUserId,
                'subscribeButton' => $subscribeButtonText
            ]);
        }
        catch (\PDOException $e) {
            $this->render('index/error');
        }
    }


    public function editProfileAction() {
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
                    //old pass validation with encryption
                    $userFromDB = $userDao->getById($userId);
                    if (!password_verify($oldPass, $userFromDB->getPassword())) {
                        $errors['old-password'] = "The old password is incorrect.";
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

                    $userDao->editWithPass($user);
                    $_SESSION['user'] = $userDao->getInfo($userId);
                    http_response_code(304);
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
        }
        catch (\Exception $e) {
            $this->render('index/error');
        }
    }

    public function viewProfileAction() {
        try {
            /* @var $userDao UserDao */
            $userDao = UserDao::getInstance();
            /* @var $user User */
            $user = $_SESSION['user'];

            $userPhoto = $user->getUserPhotoUrl();
            $firstName = $user->getFirstName();
            $lastName = $user->getLastName();
            $username = $user->getUsername();
            $userId = $user->getId();
            $email = $user->getEmail();
            $dateJoined = $user->getDateJoined();

            /* @var $videoDao VideoDao */
            $videoDao = VideoDao::getInstance();

            $videos = $videoDao->getNLatestByUploaderID(10, $userId);

            /* @var $playlistDao PlaylistDao */
            $playlistDao = PlaylistDao::getInstance();

            $playlists = $playlistDao->getNLatestByCreatorID(10, $userId);

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
                'playlists' => $playlists
            ]);
        }
        catch (\Exception $e) {
            $this->render('index/error');
        }
    }

    public function getAboutPage() {
        try {
            $userId = $_GET['id'];
            $userDao = UserDao::getInstance();
            $userObj = $userDao->getById($userId);
            $userArr = array();
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

}
