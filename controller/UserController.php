<?php

namespace controller;

use model\db\UserDao;
use model\db\VideoDao;
use model\db\PlaylistDao;
use model\User;


class UserController extends BaseController {

    public function __construct() {

    }

    public function registerAction() {
        if (isset($_POST['register'])) {
            $userModel = UserDao::getInstance();
            $user = new User();
            $user->setFirstName($_POST['firstName']);
            $user->setLastName($_POST['lastName']);
            $user->setEmail($_POST['email']);
            $user->setUsername($_POST['username']);
            $user->setPassword($_POST['password']);
            $user->setDateJoined(date("Y-m-d"));

            if (!empty($_FILES['photo']['name']) && $_FILES['photo']['size'] != 0) {
                if (!file_exists("../uploads/user_photos")) {
                    mkdir("../uploads/user_photos", 0777);
                }
                $realFileName = $_FILES['photo']['name'];
                $imgName = 'IMG_' . time();
                $imgPath = "../uploads/user_photos/$imgName." . pathinfo($realFileName, PATHINFO_EXTENSION);
                move_uploaded_file($_FILES['photo']['tmp_name'], $imgPath);
                $user->setUserPhotoUrl($imgPath);
            } else {
                $user->setUserPhotoUrl('../uploads/default_photo.png');
            }

            $success = $userModel->insert($user);
            if ($success) {
                $_SESSION['user'] = $user;
                header('Location:index.php?page=register-success');
            } else {
//                echo "Registration was unsuccessful. Try again.";

            }
        }
    }

    public function registerSuccess() {
        $this->renderPartial('user/register-success');
    }

    public function loginRegisterAction() {
        $this->renderPartial('user/login-register');
    }

    public function loginAction() {
        if (isset($_POST['login'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $userDao = UserDao::getInstance();
            $user = new User();
            $user->setUsername($username);
            $user->setPassword($password);
            $result = $userDao->login($user);
            if ($result === false) {
                // call loginRegisterAction with params to send it to render and to render it to view
//                echo "Invalid username or password.";
            } else {
                $_SESSION['user'] = $result;
                header("Location:index.php");
            }
        }
    }

    public function logoutAction() {
        session_start();
        session_destroy();
        header('Location:index.php');
    }

    public function subscribeAction() {
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

        $subscribersCount = $userDao->getSubscribersCount($profileId);

        $this->jsonEncodeParams([
            'subscribers' => $subscribersCount
        ]);
    }

    public function viewUserAction() {
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
            'loggedUserId' => $loggedUserId
        ]);
    }

    public function editProfileAction() {
        $userDao = UserDao::getInstance();
        $user = new User();

        if (isset($_POST['edit'])) {
            $userId = $_POST['userId'];
            $username = $_POST['username'];
            $firstName = $_POST['firstName'];
            $lastName = $_POST['lastName'];
            $email = $_POST['email'];
            $newPass = $_POST['newPass'];
            $confirmNewPass = $_POST['confirmNewPass'];
            $oldPass = $_POST['oldPass'];
            $imgPath = $_SESSION['user']->getUserPhotoUrl();
            if (!empty($_FILES['photo']['name']) && $_FILES['photo']['size'] != 0) {
                if (!file_exists("../uploads/user_photos")) {
                    mkdir("../uploads/user_photos", 0777);
                }
                $realFileName = $_FILES['photo']['name'];
                $imgName = 'IMG_' . time();
                $imgPath = "../uploads/user_photos/$imgName." . pathinfo($realFileName, PATHINFO_EXTENSION);
                move_uploaded_file($_FILES['photo']['tmp_name'], $imgPath);
            }

            $user->setId($userId);
            $user->setUsername($username);
            $user->setFirstName($firstName);
            $user->setLastName($lastName);
            $user->setEmail($email);
            $user->setUserPhotoUrl($imgPath);
            $user->setPassword($newPass);

            $success = $userDao->edit($user);

            if ($success) {
                $user = $userDao->getById($userId);
                $_SESSION['user'] = $user;
                $userPhoto = $user->getUserPhotoUrl();
                $firstName = $user->getFirstName();
                $lastName = $user->getLastName();
                $username = $user->getUsername();
                $userId = $user->getId();
                $email = $user->getEmail();
                $dateJoined = $user->getDateJoined();

                $videoDao = VideoDao::getInstance();
                $videos = $videoDao->getNLatestByUploaderID(10, $userId);
                $subscribersCount = $userDao->getSubscribersCount($userId);
                $subscriptionsCount = $userDao->getSubscriptionsCount($userId);

                header('Location:index.php?page=profile&id=' . $userId);
            }
        } else {
            $userId = $_GET['id'];
            /* @var $user User */
            $user = $userDao->getById($userId);
            $username = $user->getUsername();
            $firstName = $user->getFirstName();
            $lastName = $user->getLastName();
            $email = $user->getEmail();

            $this->renderPartial('user/edit-profile' ,[
                'userId' => $userId,
                'username' => $username,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'email' => $email,
            ]);
        }
    }

    public function viewProfileAction() {
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

}
