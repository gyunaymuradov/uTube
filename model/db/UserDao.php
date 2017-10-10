<?php

namespace model\db;

use model\db\DBManager;
use model\User;

class UserDao {
    private static $instance;
    private $pdo;

    private function __construct() {
        $this->pdo = DBManager::getInstance()->dbConnect();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new UserDao();
        }
        return self::$instance;
    }

    public function insertUser(User $user) {
        $statement = $this->pdo->prepare("INSERT INTO users (username, password, email, firs_name, last_name, user_photo_url) VALUES (?, ?, ?, ?, ?)");
        $statement->execute(array($user->getUsername(), $user->getPassword(), $user->getEmail(), $user->getFirstName(), $user->getLastName(), $user->getUserPhotoUrl()));
        $user->setId($this->pdo->lastInsertId());
    }

    public function checkIfUserAlreadyExists(User $user) {
        $statement = $this->pdo->prepare("SELECT COUNT(*) as number FROM users WHERE username = ?");
        $statement->execute(array($user->getUsername()));
        return $statement->fetch(\PDO::FETCH_ASSOC)['number'] > 0;
    }
}