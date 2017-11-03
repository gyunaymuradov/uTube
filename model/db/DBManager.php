<?php

namespace model\db;

class DBManager {
    private static $instance;
    private $pdo;
    const DB_IP = "127.0.0.1";
    const DB_PORT = "3306";
    const DB_NAME = "utube";
    const DB_USER = "root";
    const DB_PASS = "";

    private function __construct() {
        try {
            $this->pdo = new \PDO('mysql:host=' . self::DB_IP . ':' . self::DB_PORT . ';dbname='
                                    . self::DB_NAME, self::DB_USER, self::DB_PASS);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
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