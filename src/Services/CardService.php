<?php

namespace Marketplace\Services;

use Marketplace\Models\CardModel;
use \Firebase\JWT\JWT;

class CardService
{
    private $cardModel;
    private $jwtKey;

    public function __construct($pdo, $jwtKey)
    {
        $this->cardModel = new CardModel($pdo);
        $this->jwtKey = $jwtKey;
    }

    public function verifyCardCredentials($cardNum, $pin)
    {
        $result = $this->cardModel->verifyCard($cardNum, $pin);

        if ($result) {
            return $this->generateJWT($result['card_num']);
        } else {
            return false;
        }
    }

    private function generateJWT($cardNum)
    {
        $payload = [
            "iss" => "Marketplace",
            "iat" => time(),
            "exp" => time() + 300, // Expiration time (5 minutes from now)
            "card_num" => $cardNum
        ];

        return JWT::encode($payload, $this->jwtKey);
    }

    public function validateJWT($token)
    {
        try {
            $decoded = JWT::decode($token, $this->jwtKey, array('HS256'));
            return (array) $decoded;
        } catch (\Exception $e) {
            return false;
        }
    }
}
