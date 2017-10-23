<?php

namespace model\db;

use model\User;
use PDO;

class UserDao {

    private static $instance;
    private $pdo;

    const LOGIN = "SELECT id, username, password, email, first_name, last_name, user_photo_url, date_joined FROM users WHERE username = ?";
    const GET_INFO = "SELECT id, username, email, first_name, last_name, user_photo_url, date_joined FROM users WHERE id = ?";
    const INSERT = "INSERT INTO users (username, password, email, first_name, last_name, user_photo_url, date_joined) VALUES (?, ?, ?, ?, ?, ?, ?)";
    const EDIT = "UPDATE users SET username = ?, email = ?, first_name = ?, last_name = ? WHERE id = ?";
    const EDIT_WITH_PASS = "UPDATE users SET username = ?, password = ?, email = ?, first_name = ?, last_name = ? WHERE id = ?";
    const CHECK_FOR_USERNAME = "SELECT COUNT(*) as number FROM users WHERE username = ?";
    const GET_SUGGESTIONS_BY_USERNAME = "SELECT id, username FROM users WHERE username LIKE ?";
    const SEARCH = "SELECT id, username, CONCAT(first_name, ' ', last_name) as full_name, user_photo_url FROM users WHERE username LIKE ?";
    const GET_BY_ID = "SELECT username, first_name, last_name, email, user_photo_url, date_joined FROM users WHERE id = ?";
    const GET_SUBSCRIBERS = "SELECT u.id, u.username, user_photo_url FROM users u JOIN follows f ON u.id = f.follower_id WHERE f.followed_id = ?";
    const GET_SUBSCRIBERS_COUNT = "SELECT COUNT(*) as follower_count FROM users u JOIN follows f ON u.id = f.follower_id WHERE f.followed_id = ?";
    const GET_SUBSCRIPTIONS = "SELECT u.id, u.username, user_photo_url FROM users u JOIN follows f ON u.id = f.followed_id WHERE f.follower_id = ?";
    const GET_SUBSCRIPTIONS_COUNT = "SELECT COUNT(*) as followed_count FROM users u JOIN follows f ON u.id = f.followed_id WHERE f.follower_id = ?";
    const FOLLOW = "INSERT INTO follows (follower_id, followed_id) VALUES (?, ?)";
    const UNFOLLOW = "DELETE FROM follows WHERE follower_id = ? AND followed_id = ?";
    const CHECK_IF_FOLLOWED = "SELECT COUNT(*) as number FROM follows WHERE followed_id = ? AND follower_id = ?";
    const GET_MOST_SUBSCRIBED = "SELECT u.username, u.id, u.user_photo_url FROM users u JOIN follows f ON  u.id = f.followed_id GROUP BY followed_id ORDER BY COUNT(f.follower_id) DESC LIMIT 10";

    private function __construct() {
        $this->pdo = DBManager::getInstance()->dbConnect();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new UserDao();
        }
        return self::$instance;
    }

    public function editWithPass(User $user) {
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $statement = $this->pdo->prepare(self::EDIT_WITH_PASS);
        $statement->execute(array($user->getUsername(), $user->getPassword(), $user->getEmail(), $user->getFirstName(), $user->getLastName(), $user->getId()));
        $rowsAffected = $statement->rowCount();
        return $rowsAffected;
    }

    /**
     * @param User $user
     * @return mixed|User
     */
    public function login(User $user) {
        $statement = $this->pdo->prepare(self::LOGIN);
        $statement->execute(array($user->getUsername()));
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if (!empty($result) && password_verify($user->getPassword(), $result['password'])) {
            $userFromDb = new User();
            $userFromDb->setId($result['id']);
            $userFromDb->setUsername($result['username']);
            $userFromDb->setEmail($result['email']);
            $userFromDb->setFirstName($result['first_name']);
            $userFromDb->setLastName($result['last_name']);
            $userFromDb->setUserPhotoUrl($result['user_photo_url']);
            $userFromDb->setDateJoined($result['date_joined']);
            $userFromDb->setSubscribers(self::getSubscribersCount($result['id']));
            $userFromDb->setSubscriptions(self::getSubscriptionsCount($result['id']));
            return $userFromDb;
        }
        return false;
    }

    public function getInfo($id) {
        $statement = $this->pdo->prepare(self::GET_INFO);
        $statement->execute(array($id));
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if (!empty($result)) {
            $userFromDb = new User();
            $userFromDb->setId($result['id']);
            $userFromDb->setUsername($result['username']);
            $userFromDb->setEmail($result['email']);
            $userFromDb->setFirstName($result['first_name']);
            $userFromDb->setLastName($result['last_name']);
            $userFromDb->setUserPhotoUrl($result['user_photo_url']);
            $userFromDb->setDateJoined($result['date_joined']);
            $userFromDb->setSubscribers(self::getSubscribersCount($result['id']));
            $userFromDb->setSubscriptions(self::getSubscriptionsCount($result['id']));
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
            $user->getFirstName(), $user->getLastName(), $user->getUserPhotoUrl(),
            $user->getDateJoined()));
        return $result;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function edit(User $user) {
        $statement = $this->pdo->prepare(self::EDIT);
        $statement->execute(array($user->getUsername(), $user->getEmail(), $user->getFirstName(), $user->getLastName(), $user->getId()));
        $rowsAffected = $statement->rowCount();
        return $rowsAffected;
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
    public function getSuggestionsByUsername($username) {
        $statement = $this->pdo->prepare(self::GET_SUGGESTIONS_BY_USERNAME);
        $statement->execute(array("$username%"));
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * @param $id
     * @return User
     */
    public function getById($id) {
        $statement = $this->pdo->prepare(self::GET_BY_ID);
        $statement->execute(array($id));
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            return null;
        }
        $user = new User();
        $user->setId($id);
        $user->setUsername($result['username']);
        $user->setFirstName($result['first_name']);
        $user->setLastName($result['last_name']);
        $user->setEmail($result['email']);
        $user->setUserPhotoUrl($result['user_photo_url']);
        $user->setDateJoined($result['date_joined']);

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
        $usersArr = $statement->fetchAll(PDO::FETCH_ASSOC);
        $users = array();
        foreach ($usersArr as $userArr) {
            $user = new User();
            $user->setId($userArr['id']);
            $user->setUsername($userArr['username']);
            $user->setUserPhotoUrl($userArr['user_photo_url']);
            $users[] = $user;
        }
        return $users;
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

    public function getMostSubscribed() {
        $statement = $this->pdo->prepare(self::GET_MOST_SUBSCRIBED);
        $statement->execute();
        $usersArr = $statement->fetchAll(PDO::FETCH_ASSOC);
        $users = array();
        foreach ($usersArr as $userArr) {
            $user = new User();
            $user->setId($userArr['id']);
            $user->setUserPhotoUrl($userArr['user_photo_url']);
            $user->setUsername($userArr['username']);
            $users[] = $user;
        }
        return $users;
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
    public function checkIfFollowed($followedId, $followerId) {
        $statement = $this->pdo->prepare(self::CHECK_IF_FOLLOWED);
        $statement->execute(array($followedId, $followerId));
        return $statement->fetch(PDO::FETCH_ASSOC)['number'] > 0;
    }

    public function search($username) {
        $statement = $this->pdo->prepare(self::SEARCH);
        $statement->execute(array("%$username%"));
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
}
