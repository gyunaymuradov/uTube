<?php

namespace controller;

use model\db\UserDao;
use model\User;

class UserController extends BaseController {

    public function __construct() {

    }

    public function logoutAction() {
        session_start();
        session_destroy();
        header('Location:index.php');
    }
    
    public function loginRegisterAction() {
        $this->renderPartial('user/login-register');
    }

    public function registerSuccess() {
        $this->renderPartial('user/register-success');
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

            if (!empty($_FILES['photo']['name']) && $_FILES['photo']['size'] == 0) {
                $name = basename($_FILES["photo"]["name"]);
                move_uploaded_file($_FILES['photo']['tmp_name'], '../uploads/' . $name);
                $user->setUserPhotoUrl('/uploads/' . $name);
            } else {
                $user->setUserPhotoUrl('uploads/default_photo.png');
            }

            $success = $userModel->insert($user);
            if ($success) {
                $_SESSION['user'] = $user;
                header('Location:index.php?page=register-success');
            } else {
//                echo "Registration was unsuccessful. Try again.";
//                require_once '../view/start.html';
            }
        }
    }

    public function loginAction() {
        if (isset($_POST['login'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $userModel = UserDao::getInstance();
            $user = new User();
            $user->setUsername($username);
            $user->setPassword($password);
            $result = $userModel->login($user);
            if ($result === false) {
                // call loginRegisterAction with params to send it to render and to render it to view
//                echo "Invalid username or password.";
//                require_once '../view/start.html';
            } else {
                $_SESSION['user'] = $result;
                header("Location:index.php");
            }
        }
    }

    public function editProfileAction() {
        
    }

    public function viewProfileAction() {
        /* @var $user \model\User */
        $user = null;
        /* @var $userDao \model\db\UserDao */
        $userDao = \model\db\UserDao::getInstance();
        $logged = 'false';
        if (!isset($_SESSION['user'])) {
            $user = $userDao->getById($_GET['id']);
        } else {
            /* @var $loggedUser \model\User */
            $loggedUser = $_SESSION['user'];
            $logged = 'true';
            /* @var $watchedUser \model\User */
            $watchedUser = $userDao->getById($_GET['id']);

            if ($loggedUser->getId() != $watchedUser->getId()) {
                $user = $watchedUser;
            } else {
                $user = $loggedUser;
            }

        }

        $userPhoto = $user->getUserPhotoUrl();
        $firstName = $user->getFirstName();
        $lastName = $user->getLastName();
        $username = $user->getUsername();
        $userId = $user->getId();
        $email = $user->getEmail();
        $dateJoined = $user->getDateJoined();

        /* @var $videoDao \model\db\VideoDao */
        $videoDao = \model\db\VideoDao::getInstance();

        $videos = $videoDao->getNLatestByUploaderID(10, $userId);

        $subscribersCount = $userDao->getSubscribersCount($userId);
        $subscriptionsCount = $userDao->getSubscriptionsCount($userId);

//        $visible = true;
//        if ($_GET['id'] == $user->getId()) {
//            $visible = false;
//        } else {
//            if (!isset($_SESSION['user'])) {
//                $class = 'disabled';
//            }
//        }

        $this->render('user/view-profile', [
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
            'logged' => $logged,
            'subscribeBtnVisibility' => $logged == 'true' && $_SESSION['user']->getId() == $userId ? 'none' : 'block'
        ]);
    }

}
