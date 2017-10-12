<?php

namespace model\db;

use model\User;
use PDO;

class UserDao {

    private static $instance;
    private $pdo;

    const LOGIN = "SELECT id, username, email, first_name, last_name FROM users WHERE username = ? AND password = ?";
    const INSERT = "INSERT INTO users (username, password, email, first_name, last_name, user_photo_url) VALUES (?, ?, ?, ?, ?, ?)";
    const EDIT = "UPDATE TABLE users SET (username, password, email, first_name, last_name, user_photo_url) VALUES (?, ?, ?, ?, ?, ?) WHERE id = ?";
    const CHECK_FOR_USERNAME = "SELECT COUNT(*) as number FROM users WHERE username = ?";
    const SEARCH_BY_USERNAME = "SELECT id, username FROM users WHERE username LIKE '%?%'";
    const GET_BY_ID = "SELECT username, first_name, last_name, email, user_photo_url FROM users WHERE id = ?";
    const GET_SUBSCRIBERS = "SELECT u.id, u.username, user_photo_url FROM users u JOIN follows f ON u.id = f.follower_id WHERE f.followed_id = ?";
    const GET_SUBSCRIBERS_COUNT = "SELECT COUNT(*) as follower_count FROM users u JOIN follows f ON u.id = f.follower_id WHERE f.followed_id = ?";
    const GET_SUBSCRIPTIONS = "SELECT u.id, u.username, user_photo_url FROM users u JOIN follows f ON u.id = f.followed_id WHERE f.follower_id = ?";
    const GET_SUBSCRIPTIONS_COUNT = "SELECT COUNT(*) as followed_count FROM users u JOIN follows f ON u.id = f.followed_id WHERE f.follower_id = ?";
    const FOLLOW = "INSERT INTO follows (follower_id, followed_id) VALUES (?, ?)";
    const UNFOLLOW = "DELETE FROM follows WHERE follower_id = ? AND followed_id = ?";
    const CHECK_IF_FOLLOWED = "SELECT COUNT(*) as number FROM follows WHERE followed_id = ? AND follower_id = ?";

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
    public function login(User $user) {
        $statement = $this->pdo->prepare(self::LOGIN);
        $statement->execute(array($user->getUsername(), $user->getPassword()));
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if (!empty($result)) {
            $userFromDb = new User();
            $userFromDb->setId($result['id']);
            $userFromDb->setUsername($result['username']);
            $userFromDb->setEmail($result['email']);
            $userFromDb->setFirstName($result['first_name']);
            $userFromDb->setLastName($result['last_name']);
            $userFromDb->setSubscriptions(self::getSubscribers($result['id']));
            return $userFromDb;
        }
        return $result;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function insert(User $user) {
        $statement = $this->pdo->prepare(self::INSERT);
        $result = $statement->execute(array(
            $user->getUsername(), $user->getPassword(), $user->getEmail(),
            $user->getFirstName(), $user->getLastName(), $user->getUserPhotoUrl()));
        return $result;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function edit(User $user) {
        $statement = $this->pdo->prepare(self::EDIT);
        $result = $statement->execute(array($user->getUsername(), $user->getPassword(), $user->getEmail(), $user->getFirstName(), $user->getLastName(), $user->getUserPhotoUrl(), $user->getId()));
        return $result;
    }

    /**
     * @param string $username
     * @return bool
     */
    public function checkIfExists($username) {
        $statement = $this->pdo->prepare(self::CHECK_FOR_USERNAME);
        $statement->execute(array($username));
        return $statement->fetch(PDO::FETCH_ASSOC)['number'] > 0;
    }

    /**
     * @param string $username
     * @return array
     */
    // will be implemented to search with ajax on key up event later
    public function searchByUsername($username) {
        $statement = $this->pdo->prepare(self::SEARCH_BY_USERNAME);
        $statement->execute(array($username));
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @param $id
     * @return User
     */
    public function getById($id) {
        $statement = $this->pdo->prepare(self::GET_BY_ID);
        $statement->execute(array($id));
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        $user = new User();
        $user->setId($id);
        $user->setUsername($result['username']);
        $user->setFirstName($result['first_name']);
        $user->setLastName($result['last_name']);
        $user->setEmail($result['email']);
        $user->setUserPhotoUrl($result['user_photo_url']);

        return $user;
    }

    /**
     * @param int $id
     * @return array
     */
    public function getSubscribers($id) {
        $statement = $this->pdo->prepare(self::GET_SUBSCRIBERS);
        $statement->execute(array($id));
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param int $id
     * @return int
     */
    public function getSubscribersCount($id) {
        $statement = $this->pdo->prepare(self::GET_SUBSCRIBERS_COUNT);
        $statement->execute(array($id));
        return $statement->fetch(PDO::FETCH_ASSOC)['follower_count'];
    }

    /**
     * @param int $id
     * @return array
     */
    public function getSubscriptions($id) {
        $statement = $this->pdo->prepare(self::GET_SUBSCRIPTIONS);
        $statement->execute(array($id));
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param int $id
     * @return int
     */
    public function getSubscriptionsCount($id) {
        $statement = $this->pdo->prepare(self::GET_SUBSCRIPTIONS_COUNT);
        $statement->execute(array($id));
        return $statement->fetch(PDO::FETCH_ASSOC)['followed_count'];
    }

    /**
     * @param int $followerId
     * @param int $followedId
     * @return bool
     */
    public function follow ($followerId, $followedId) {
        $statement = $this->pdo->prepare(self::FOLLOW);
        $result = $statement->execute(array($followerId, $followedId));
        return $result;
    }

    /**
     * @param int $followerId
     * @param int $followedId
     * @return bool
     */
    public function unfollow ($followerId, $followedId) {
        $statement = $this->pdo->prepare(self::UNFOLLOW);
        $result = $statement->execute(array($followerId, $followedId));
        return $result;
    }

    /**
     * @param int $followedId
     * @param int $followerId
     * @return bool
     */
    public function checkIfUFollowed($followedId, $followerId) {
        $statement = $this->pdo->prepare(self::CHECK_IF_FOLLOWED);
        $statement->execute(array($followedId, $followerId));
        return $statement->fetch(PDO::FETCH_ASSOC)['number'] > 0;
    }
}