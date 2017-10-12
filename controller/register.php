<?php

use model\db\UserDao;
use model\User;

session_start();

function __autoload($className) {
    $className = '..\\' . $className;
    $className = str_replace("\\", "/", $className);
    require_once $className . '.php';
}

if (isset($_POST['register'])) {
    $userModel = UserDao::getInstance();
    $user = new User();
    $user->setFirstName($_POST['firstName']);
    $user->setLastName($_POST['lastName']);
    $user->setEmail($_POST['email']);
    $user->setUsername($_POST['username']);
    $user->setPassword($_POST['password']);
    if (isset($_FILES['photo'])) {
        $name = basename($_FILES["photo"]["name"]);
        move_uploaded_file($_FILES['photo']['tmp_name'], '../uploads/' . $name);
        $user->setUserPhotoUrl('/uploads/' . $name);
    } else {
        $user->setUserPhotoUrl('/uploads/default_photo.png');
    }

    $success = $userModel->insert($user);
    if ($success) {
        header('Location:../view/index.php');
    } else {
        echo "Registration was unsuccessful. Try again.";
        require_once '../view/register.html';
    }

}