<?php

namespace Marketplace\Database;

use Dotenv\Dotenv;
use Exception;
use PDO;
use PDOException;

class Connection
{
    private static $_instance;
    private $_conn;

    private $_host;
    private $_dbname; // OnlineShoppingDB
    private $_username;
    private $_password;
    private $_charset;

    private function __construct()
    {
        try {
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
            $dotenv->load();
        } catch (Exception $e) {
            die("Error loading .env: " . $e->getMessage());
        }


        $this->_host = $_ENV['DB_HOST'];
        $this->_dbname = $_ENV['DB_NAME'];
        $this->_username = $_ENV['DB_USER'];
        $this->_password = $_ENV['DB_PASS'];
        $this->_charset = $_ENV['DB_CHARSET'];


        try {
            $this->_conn = new PDO("mysql:host={$this->_host};dbname={$this->_dbname};charset={$this->_charset}", $this->_username, $this->_password);
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
