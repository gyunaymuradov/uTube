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
        $result = $statement->execute(array($user->getUsername(), $user->getPassword(), $user->getEmail(), $user->getFirstName(), $user->getLastName(), $user->getUserPhotoUrl()));
        return $result;
    }

    public function checkIfUserAlreadyExists(User $user) {
        $statement = $this->pdo->prepare("SELECT COUNT(*) as number FROM users WHERE username = ?");
        $statement->execute(array($user->getUsername()));
        return $statement->fetch(\PDO::FETCH_ASSOC)['number'] > 0;
    }

    // will be implemented to search with ajax on key up event later
    public function getUserByUsername($username) {
        $statement = $this->pdo->prepare("SELECT id, username FROM users WHERE username LIKE '%?%'");
        $statement->execute(array($username));
        return $statement->fetch(\PDO::FETCH_ASSOC);
    }

    public function getFollowersByFollowedId(User $user) {
        $statement = $this->pdo->prepare("SELECT u.id, u.username FROM users u JOIN follows f ON u.id = f.follower_id WHERE f.followed_id = ?");
        $statement->execute(array($user->getId()));
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getFollowedUsersByFollowerId(User $user) {
        $statement = $this->pdo->prepare("SELECT u.id, u.username FROM users u JOIN follows f ON u.id = f.followed_id WHERE f.follower_id = ?");
        $statement->execute(array($user->getId()));
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getFollowedUsersCountByFollowerId(User $user) {
        $statement = $this->pdo->prepare("SELECT COUNT(*) as followed_count FROM users u JOIN follows f ON u.id = f.followed_id WHERE f.follower_id = ?");
        $statement->execute(array($user->getId()));
        return $statement->fetch(\PDO::FETCH_ASSOC)['followed_count'];
    }

    public function getFollowersCountByFollowedId(User $user) {
        $statement = $this->pdo->prepare("SELECT COUNT(*) as follower_count FROM users u JOIN follows f ON u.id = f.follower_id WHERE f.followed_id = ?");
        $statement->execute(array($user->getId()));
        return $statement->fetch(\PDO::FETCH_ASSOC)['follower_count'];
    }

    public function followUser (User $follower, User $followed) {
        $statement = $this->pdo->prepare("INSERT INTO follows (follower_id, followed_id) VALUES (?, ?)");
        $result = $statement->execute(array($follower->getId(), $followed->getId()));
        return $result;
    }

    public function unfollowUser (User $follower, User $followed) {
        $statement = $this->pdo->prepare("DELETE FROM follows WHERE follower_id = ? AND followed_id = ?");
        $result = $statement->execute(array($follower->getId(), $followed->getId()));
        return $result;
    }

    public function checkIfUserIsFollowedByCurrentUser(User $follower) {
        $statement = $this->pdo->prepare("SELECT COUNT(*) as number FROM follows WHERE follower_id = ? AND followed_id = ?");
        $statement->execute(array($follower->getId()));
        return $statement->fetch(\PDO::FETCH_ASSOC)['number'];
    }
}