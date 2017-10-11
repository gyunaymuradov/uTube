<?php

namespace model\db;

use model\db\DBManager;
use \PDO;


class TagDao {
    private static $instance;
    private $pdo;

    private function __construct() {
        $this->pdo = DBManager::getInstance()->dbConnect();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new TagDao();
        }
        return self::$instance;
    }

    /**
     * Returns id of a tag by given name
     * @param string $tagName
     * @return mixed
     */
    function getTagIdByName($tagName) {
        $statement = $this->pdo->prepare("SELECT id FROM tags WHERE name=?");
        $statement->execute(array($tagName));
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result['id'];
    }

    /**
     * Returns name of a tag by given id
     * @param int $tagID
     * @return mixed
     */
    function getTagNameById($tagID) {
        $statement = $this->pdo->prepare("SELECT name FROM tags WHERE id=?");
        $statement->execute(array($tagID));
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result['name'];
    }

    //tested - works fine
    //may need to add functions for adding new tags
}