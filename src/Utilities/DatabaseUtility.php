<?php

namespace Marketplace\Utilities;

use Marketplace\Database\Connection;

class DatabaseUtility
{
    public static function createTables()
    {
        $conn = Connection::getInstance()->getConnection();

        $tables = [
            "CREATE TABLE IF NOT EXISTS card (
                card_id INT AUTO_INCREMENT PRIMARY KEY,
                card_holder_name VARCHAR(255) NOT NULL,
                card_num CHAR(16) NOT NULL UNIQUE,
                pin INT NOT NULL
            )",
            "CREATE TABLE IF NOT EXISTS user (
                user_id INT AUTO_INCREMENT PRIMARY KEY,
                balance DECIMAL(10, 2) DEFAULT 0.00 NOT NULL,
                user_address VARCHAR(255) NOT NULL
            )",
            "CREATE TABLE IF NOT EXISTS purchase (
                pur_id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                item_id INT,
                quantity INT,
                price DECIMAL(10, 2),
                seller_ip VARCHAR(255),
                date DATETIME,
                FOREIGN KEY (user_id) REFERENCES user(user_id)
            )",
            "CREATE TABLE IF NOT EXISTS item_seller1 (
                item_id INT AUTO_INCREMENT PRIMARY KEY,
                item_name VARCHAR(255) NOT NULL,
                stock_qty INT NOT NULL DEFAULT 0,
                price_of_unit DECIMAL(10, 2) NOT NULL
            )",
            "CREATE TABLE IF NOT EXISTS item_seller2 (
                item_id INT AUTO_INCREMENT PRIMARY KEY,
                item_name VARCHAR(255) NOT NULL,
                stock_qty INT NOT NULL DEFAULT 0,
                price_of_unit DECIMAL(10, 2) NOT NULL
            )"
        ];

        foreach ($tables as $sql) {
            $conn->exec($sql);
        }
    }
}
