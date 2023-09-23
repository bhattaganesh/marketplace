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
            return $this->generateJWT($result);
        } else {
            return false;
        }
    }

    private function generateJWT($cardData)
    {
        $payload = [
            "iss" => "your-issuer-identifier", // Issuer of the JWT
            "iat" => time(), // Issued at time
            "exp" => time() + 3600, // Expiration time (+1 hour)
            "card_id" => $cardData['id'] // Storing card ID in the JWT payload
        ];

        return JWT::encode($payload, $this->jwtKey);
    }

    public function validateJWT($token)
    {
        try {
            $decoded = JWT::decode($token, $this->jwtKey, array('HS256'));
            return (array) $decoded;
        } catch (\Exception $e) {
            // Token is invalid
            return false;
        }
    }
}
