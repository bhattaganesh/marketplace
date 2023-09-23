<?php

namespace Marketplace\Models;

use PDO;

class CardModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function verifyCard($cardNum, $pin)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM CARD WHERE card_num = ? AND pin = ?");
        $stmt->execute([$cardNum, $pin]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

