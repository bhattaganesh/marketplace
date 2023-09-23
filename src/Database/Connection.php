<?php

namespace Marketplace\Database;

use PDO;
use PDOException;

class Connection
{
    private static $_instance;
    private $_conn;

    private $_host = 'localhost';
    private $_dbname = 'marketplace_db'; // OnlineShoppingDB
    private $_username = 'ganesh_bhatta';
    private $_password = '40028008';
    private $_charset = 'utf8mb4';

    private function __construct()
    {
        try {
            $this->_conn = new PDO("mysql:host=$this->_host;dbname=$this->_dbname;charset=$this->_charset", $this->_username, $this->_password);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new Connection();
        }
        return self::$_instance;
    }

    public function getConnection()
    {
        return $this->_conn;
    }
}
