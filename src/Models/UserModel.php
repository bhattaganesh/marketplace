<?php

namespace Marketplace\Models;

use PDO;

class UserModel
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function getUser($userId)
    {
        $stmt = $this->conn->prepare("SELECT * FROM USER WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateUserBalance($userId, $amount)
    {
        $user = $this->getUser($userId);
        if (!$user) {
            return false;
        }

        $stmt = $this->conn->prepare("UPDATE USER SET balance = balance + ? WHERE user_id = ?");
        $stmt->execute([$amount, $userId]);

        // Return true if at least one row was affected.
        return $stmt->rowCount() > 0;
    }
}
