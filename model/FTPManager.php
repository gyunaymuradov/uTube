<?php
namespace model;

class FTPManager {
    private static $instance;
    private $ftpStream;
    const FTP_HOST = "66.220.9.50";
    const FTP_USER = "ittutube";
    const FTP_PASS = "Gyunay1Sasho";

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