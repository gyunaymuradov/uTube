<?php

namespace model\db;

use model\db\DBManager;
use model\User;
use PDO;

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

    /**
     * @param User $user
     * @return mixed|User
     */
    public function loginUser(User $user) {
        $statement = $this->pdo->prepare("SELECT id, username FROM users WHERE username = ? AND password = ?");
        $statement->execute(array($user->getUsername(), $user->getPassword()));
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if (!empty($result)) {
            $userFromDb = new User();
            $userFromDb->setUsername($result['username']);
            $userFromDb->setId($result['id']);
            return $userFromDb;
        }
        return $result;
    }
    
    /**
     * @param User $user
     * @return bool
     */
    public function insertUser(User $user) {
        $statement = $this->pdo->prepare("INSERT INTO users (username, password, email, first_name, last_name, user_photo_url) VALUES (?, ?, ?, ?, ?, ?)");
        $result = $statement->execute(array($user->getUsername(), $user->getPassword(), $user->getEmail(), $user->getFirstName(), $user->getLastName(), $user->getUserPhotoUrl()));
        return $result;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function editUser(User $user) {
        $statement = $this->pdo->prepare("UPDATE TABLE users SET (username, password, email, first_name, last_name, user_photo_url) VALUES (?, ?, ?, ?, ?, ?) WHERE id = ?");
        $result = $statement->execute(array($user->getUsername(), $user->getPassword(), $user->getEmail(), $user->getFirstName(), $user->getLastName(), $user->getUserPhotoUrl(), $user->getId()));
        return $result;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function checkIfUserAlreadyExists(User $user) {
        $statement = $this->pdo->prepare("SELECT COUNT(*) as number FROM users WHERE username = ?");
        $statement->execute(array($user->getUsername()));
        return $statement->fetch(PDO::FETCH_ASSOC)['number'] > 0;
    }

    /**
     * @param string $username
     * @return array
     */
    // will be implemented to search with ajax on key up event later
    public function searchUsersByUsername($username) {
        $statement = $this->pdo->prepare("SELECT id, username FROM users WHERE username LIKE '%?%'");
        $statement->execute(array($username));
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserById($id) {
        $statement = $this->pdo->prepare("SELECT username, first_name, last_name, email FROM users WHERE id = ?");
        $statement->execute(array($id));
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        $user = new User();
        $user->setUsername($result['username']);
        $user->setFirstName($result['first_name']);
        $user->setLastName($result['last_name']);
        $user->setEmail($result['email']);

        return $user;
    }

    /**
     * @param User $user
     * @return array
     */
    public function getFollowersByFollowedId(User $user) {
        $statement = $this->pdo->prepare("SELECT u.id, u.username FROM users u JOIN follows f ON u.id = f.follower_id WHERE f.followed_id = ?");
        $statement->execute(array($user->getId()));
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param User $user
     * @return array
     */
    public function getFollowedUsersByFollowerId(User $user) {
        $statement = $this->pdo->prepare("SELECT u.id, u.username FROM users u JOIN follows f ON u.id = f.followed_id WHERE f.follower_id = ?");
        $statement->execute(array($user->getId()));
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param User $user
     * @return int
     */
    public function getFollowedUsersCountByFollowerId(User $user) {
        $statement = $this->pdo->prepare("SELECT COUNT(*) as followed_count FROM users u JOIN follows f ON u.id = f.followed_id WHERE f.follower_id = ?");
        $statement->execute(array($user->getId()));
        return $statement->fetch(PDO::FETCH_ASSOC)['followed_count'];
    }

    /**
     * @param User $user
     * @return int
     */
    public function getFollowersCountByFollowedId(User $user) {
        $statement = $this->pdo->prepare("SELECT COUNT(*) as follower_count FROM users u JOIN follows f ON u.id = f.follower_id WHERE f.followed_id = ?");
        $statement->execute(array($user->getId()));
        return $statement->fetch(PDO::FETCH_ASSOC)['follower_count'];
    }

    /**
     * @param User $follower
     * @param User $followed
     * @return bool
     */
    public function followUser (User $follower, User $followed) {
        $statement = $this->pdo->prepare("INSERT INTO follows (follower_id, followed_id) VALUES (?, ?)");
        $result = $statement->execute(array($follower->getId(), $followed->getId()));
        return $result;
    }

    /**
     * @param User $follower
     * @param User $followed
     * @return bool
     */
    public function unfollowUser (User $follower, User $followed) {
        $statement = $this->pdo->prepare("DELETE FROM follows WHERE follower_id = ? AND followed_id = ?");
        $result = $statement->execute(array($follower->getId(), $followed->getId()));
        return $result;
    }

    /**
     * @param User $followed
     * @param User $follower
     * @return bool
     */
    public function checkIfUserIsFollowedByCurrentUser(User $followed, User $follower) {
        $statement = $this->pdo->prepare("SELECT COUNT(*) as number FROM follows WHERE followed_id = ? AND follower_id = ?");
        $statement->execute(array($followed->getId(), $follower->getId()));
        return $statement->fetch(PDO::FETCH_ASSOC)['number'] > 0;
    }
}