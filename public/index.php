<?php

require '../vendor/autoload.php';

use Marketplace\Database\Connection;
use Marketplace\Services\BalanceService;
use Marketplace\Services\CardService;
use Marketplace\Services\ItemSearchService;
use Marketplace\Services\ItemService;
use Marketplace\Services\PurchaseService;
use Marketplace\Utilities\DatabaseUtility;

class App
{
    private $db;
    private $requestMethod;
    private $pathInfo;
    private $data;

    public function __construct()
    {
        $this->redirectToHttps();
        $this->db = Connection::getInstance()->getConnection();
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
        $this->pathInfo = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->data = $this->getRequestData();

        // Initialize tables.
        DatabaseUtility::createTables();
    }

    public function run()
    {
        $response = $this->routeRequest();
        $this->sendResponse($response);
    }

    private function redirectToHttps()
    {
        if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") {
            $location = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: ' . $location);
            exit;
        }
    }

    private function getRequestData()
    {
        $json = file_get_contents('php://input');
        return json_decode($json, true) ?? $_POST;
    }

    private function routeRequest()
    {
        $method = $this->requestMethod;
        $path = $this->pathInfo;

        if ($method === 'GET' && $path === '/seller1/items') {
            return $this->getItemList();
        }

        if ($method === 'GET' && $path === '/seller2/items') {
            return $this->getItemList('Seller2');
        }


        if ($method === 'GET' && $path === '/items') {
            return $this->searchForItems();
        }

        // Endpoint: Purchase item
        if ($method === 'POST' && $path === '/purchase-item') {
            $token = $this->data['token'] ?? null;

            if (!$this->validateToken($token)) {
                return ['success' => false, 'message' => 'Invalid or expired token.'];
            }

            return $this->purchaseItem();
        }


        if ($method === 'GET' && $path === '/search-purchase') {
            return $this->searchPurchase();
        }

        if ($method === 'DELETE' && $path === '/cancel-purchase') {
            return $this->cancelPurchase();
        }

        // Endpoint: Add Balance
        if ($method === 'POST' && $path === '/add-balance') {
            $token = $this->data['token'] ?? null;

            if (!$this->validateToken($token)) {
                return ['success' => false, 'message' => 'Invalid or expired token.'];
            }

            $amount = $this->data['amount'] ?? 0;
            $userId = $this->data['userId'] ?? 0;

            $balanceService = new BalanceService($this->db);
            return $balanceService->addUserBalance($userId, $amount);
        }

        // Endpoint: Card Check
        if ($method === 'POST' && $path === '/card-check') {
            $cardNum = $this->data['cardNum'] ?? null;
            $pin = $this->data['pin'] ?? null;

            $cardService = $this->getCardService();

            $jwt = $cardService->verifyCardCredentials($cardNum, $pin);

            if ($jwt) {
                return ['status' => 'success', 'token' => $jwt];
            } else {
                return ['status' => 'error', 'message' => 'Invalid card credentials'];
            }
        }

        return $this->handleInvalidEndpoint();
    }

    private function handleInvalidEndpoint()
    {
        header('HTTP/1.1 404 Not Found');
        return ['status' => 'error', 'message' => 'Invalid endpoint'];
    }

    private function sendResponse($response)
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        echo json_encode($response);
    }

    private function getItemList($seller = 'Seller1')
    {
        $itemService = new ItemService($this->db);
        $items = $itemService->getItemsForSeller($seller);

        if ($items) {
            return ['success' => true, 'data' => $items];
        } else {
            return ['success' => false, 'message' => 'No items found.'];
        }
    }

    private function searchForItems()
    {
        $searchTerm = $_GET['search'] ?? null;
        if (!$searchTerm) {
            return ['success' => false, 'message' => 'Search term not provided.'];
        }

        $itemSearchService = new ItemSearchService();
        $results = $itemSearchService->searchItems($searchTerm);

        return ['success' => true, 'items' => $results];
    }

    private function purchaseItem()
    {
        $itemId = $this->data['itemId'] ?? 0;
        $userId = $this->data['userId'] ?? 0;
        $quantity = $this->data['quantity'] ?? 1;
        $seller = $this->data['seller'] ?? null; // Expected values are either Seller1 or Seller2.

        $purchaseService = new PurchaseService($this->db);
        return $purchaseService->makePurchase($userId, $itemId, $quantity, $seller);
    }


    private function searchPurchase()
    {
        $purId = $_GET['purId'] ?? 0;
        $userId = $_GET['userId'] ?? 0;

        $purchaseService = new PurchaseService($this->db);

        if ($purId) {
            return $purchaseService->getPurchaseDetails($purId);
        } elseif ($userId) {
            return $purchaseService->getPurchasesByUserId($userId);
        } else {
            return ['success' => false, 'message' => 'Neither purId nor userId provided.'];
        }
    }

    private function cancelPurchase()
    {
        $purchaseId = $this->data['purchaseId'] ?? 0;
        $purchaseService = new PurchaseService($this->db);
        return $purchaseService->cancelPurchase($purchaseId);
    }

    private function getCardService()
    {
        $jwtKey = $_ENV['JWT_SECRET_KEY'];
        return new CardService($this->db, $jwtKey);
    }

    private function validateToken($token)
    {
        $cardService = $this->getCardService();
        if (!$cardService->validateJWT($token)) {
            return false;
        }
        return true;
    }
}

$app = new App();
$app->run();
