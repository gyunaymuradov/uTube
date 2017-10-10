<?php

namespace model\db;

use model\db\DBManager;
use model\User;

class VideoDao {
    private static $instance;
    private $pdo;

    private function __construct() {
        $this->pdo = DBManager::getInstance()->dbConnect();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new VideoDao();
        }
        return self::$instance;
    }
}