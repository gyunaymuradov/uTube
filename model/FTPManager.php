<?php
namespace model;

class FTPManager {
    private static $instance;
    private $ftpStream;
    const FTP_HOST = "ftp.byethost17.com";
    const FTP_USER = "b17_20966145";
    const FTP_PASS = "@Lexandar123";

    private function __construct() {
        $this->ftpStream = ftp_connect(self::FTP_HOST);
        ftp_login($this->ftpStream, self::FTP_USER, self::FTP_PASS);
    }

    public function getStream() {
        return $this->ftpStream;
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new FTPManager();
        }
        return self::$instance;
    }
}