<?php

namespace model\db;

class DBManager {
    private static $instance;
    private $pdo;
    const DB_IP = "";
    const DB_PORT = "";
    const DB_NAME = "";
    const DB_USER = "";
    const DB_PASS = "";

    private function __construct() {
        try {
            $this->pdo = new \PDO('mysql:host=' . self::DB_IP . ':' . self::DB_PORT . ';dbname='
                                    . self::DB_NAME, self::DB_USER, self::DB_PASS);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->pdo->query("USE" . self::DB_NAME);
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function dbConnect() {
        return $this->pdo;
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new DBManager();
        }
        return self::$instance;
    }
}