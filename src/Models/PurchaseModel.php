<?php

namespace Marketplace\Models;

use PDO;

class PurchaseModel
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function getPurchase($purId)
    {
        $stmt = $this->conn->prepare("SELECT * FROM PURCHASE WHERE pur_id = ?");
        $stmt->execute([$purId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insertPurchase($data)
    {
        $stmt = $this->conn->prepare("INSERT INTO PURCHASE(user_id, item_id, quantity, price, seller_id, date) VALUES(?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$data['user_id'], $data['item_id'], $data['quantity'], $data['price'], $data['seller_id']]);
        return $this->conn->lastInsertId();
    }

    public function deletePurchase($purId)
    {
        $stmt = $this->conn->prepare("DELETE FROM PURCHASE WHERE pur_id = ?");
        $stmt->execute([$purId]);
        return $stmt->rowCount();
    }

    public function getPurchasesForUser($userId)
    {
        $stmt = $this->conn->prepare("SELECT * FROM PURCHASE WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
