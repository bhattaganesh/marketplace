<?php

namespace Marketplace\Models;

use PDO;

class ItemModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getItemsBySeller($seller)
    {
        $stmt = $this->conn->prepare("SELECT * FROM item_$seller");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getItemById($itemId, $seller)
    {
        if ('127.0.0.1:8000' == $seller) {
            $seller = 'seller2';
        } else if ('127.0.0.1:8080' == $seller) {
            $seller = 'seller1';
        }

        $stmt = $this->conn->prepare("SELECT * FROM item_$seller WHERE item_id = ?");
        $stmt->execute([$itemId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
