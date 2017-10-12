<?php

use model\db\UserDao;
use model\User;

session_start();

function __autoload($className) {
    $className = '..\\' . $className;
    $className = str_replace("\\", "/", $className);
    require_once $className . '.php';
}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $userModel = UserDao::getInstance();
    $user = new User();
    $user->setUsername($username);
    $user->setPassword($password);
    $result = $userModel->login($user);
    if ($result === false) {
        echo "Invalid username or password.";
        require_once '../view/login.html';
    } else {
        $_SESSION['userId'] = $result->getId();
        header("Location:../view/index.php");
    }
}