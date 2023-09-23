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
        $stmt = $this->conn->prepare("SELECT * FROM ITEM_$seller");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getItemById($itemId, $seller)
    {
        $stmt = $this->conn->prepare("SELECT * FROM ITEM_$seller WHERE item_id = ?");
        $stmt->execute([$itemId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
