<?php

namespace model\db;

class DBManager {
    private static $instance;
    private $pdo;
    const DB_IP = "sql11.freemysqlhosting.net";
    const DB_PORT = "3306";
    const DB_NAME = "sql11202452";
    const DB_USER = "sql11202452";
    const DB_PASS = "aKVaANdHcS";


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