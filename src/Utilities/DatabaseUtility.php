<?php

namespace Marketplace\Utilities;

use Marketplace\Database\Connection;

class DatabaseUtility
{
    public static function createTables()
    {
        $conn = Connection::getInstance()->getConnection();

        $tables = [
            "CREATE TABLE IF NOT EXISTS CARD (
                card_holder_name VARCHAR(255) NOT NULL,
                card_num VARCHAR(255) PRIMARY KEY,
                pin INT NOT NULL
            )",
            "CREATE TABLE IF NOT EXISTS USER (
                user_id INT AUTO_INCREMENT PRIMARY KEY,
                balance DECIMAL(10, 2),
                user_address VARCHAR(255)
            )",
            "CREATE TABLE IF NOT EXISTS PURCHASE (
                pur_id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                item_id INT,
                quantity INT,
                price DECIMAL(10, 2),
                seller_id INT,
                date DATETIME,
                FOREIGN KEY (user_id) REFERENCES USER(user_id)
            )",
            "CREATE TABLE IF NOT EXISTS ITEM_Seller1 (
                item_id INT AUTO_INCREMENT PRIMARY KEY,
                item_name VARCHAR(255),
                stock_qty INT,
                price_of_unit DECIMAL(10, 2)
            )",
            "CREATE TABLE IF NOT EXISTS ITEM_Seller2 (
                item_id INT AUTO_INCREMENT PRIMARY KEY,
                item_name VARCHAR(255),
                stock_qty INT,
                price_of_unit DECIMAL(10, 2)
            )"
        ];

        foreach ($tables as $sql) {
            $conn->exec($sql);
        }
    }
}
