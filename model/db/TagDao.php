<?php
namespace model\db;
use model\db\DBManager;
use \PDO;
class TagDao {
    private static $instance;
    private $pdo;
    const GET_TAGS_BY_ID = "SELECT name FROM tags WHERE id=?";
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
     * Returns name of a tag by given id
     * @param int $tagID
     * @return mixed
     */
    function getTagNameById($tagID) {
        $statement = $this->pdo->prepare(self::GET_TAGS_BY_ID);
        $statement->execute(array($tagID));
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result['name'];
    }
    //tested - works fine
    //may need to add functions for adding new tags
}
