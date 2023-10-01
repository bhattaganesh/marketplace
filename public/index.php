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
        $this->routeRequest();
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

        if ($method === 'GET' && $path === '/frontend') {
            include_once '../frontend/index.php';
        }

        if ($method === 'GET' && $path === '/seller1/items') {
            return $this->getItemList();
        }

        if ($method === 'GET' && $path === '/seller2/items') {
            return $this->getItemList('seller2');
        }


        if ($method === 'GET' && $path === '/items') {
            return $this->searchForItems();
        }

        // Endpoint: Purchase item
        if ($method === 'POST' && $path === '/purchase-item') {
            $token = $this->data['token'] ?? null;

            if (!$this->validateToken($token)) {
                $this->sendResponse(['success' => false, 'message' => 'Invalid or expired token.'], 401);
                return;
            }

            $this->sendResponse($this->purchaseItem(), 201);
            return;
        }


        if ($method === 'GET' && $path === '/search-purchase') {
            $this->sendResponse($this->searchPurchase(), 200);
            return;
        }

        if ($method === 'DELETE' && $path === '/cancel-purchase') {
            $token = $this->data['token'] ?? null;

            if (!$this->validateToken($token)) {
                $this->sendResponse(['success' => false, 'message' => 'Invalid or expired token.'], 401);
                return;
            }

            $this->sendResponse($this->cancelPurchase(), 200);
            return;
        }

        // Endpoint: Add Balance
        if ($method === 'POST' && $path === '/add-balance') {
            $token = $this->data['token'] ?? null;

            if (!$this->validateToken($token)) {
                $this->sendResponse(['success' => false, 'message' => 'Invalid or expired token.'], 401);
                return;
            }

            $amount = $this->data['amount'] ?? 0;
            $userId = $this->data['userId'] ?? 0;
            $balanceService = new BalanceService($this->db);
            $result = $balanceService->addUserBalance($userId, $amount);
            if (false === $result) {
                $this->sendResponse(['success' => false, 'message' => 'User not found.'], 400);
            }

            if (null === $result) {
                $this->sendResponse(['success' => false, 'message' => 'Failed to update balance.'], 500);
            }

            $this->sendResponse(['success' => true, 'message' => 'Balance updated successfully.'], 201);
            return;
        }

        // Endpoint: Card Check
        if ($method === 'POST' && $path === '/card-check') {
            $cardNum = $this->data['cardNum'] ?? null;
            $pin = $this->data['pin'] ?? null;

            $cardService = $this->getCardService();
            $jwt = $cardService->verifyCardCredentials($cardNum, $pin);

            if ($jwt) {
                $this->sendResponse(['status' => 'success', 'token' => $jwt], 200);
                return;
            } else {
                $this->sendResponse(['status' => 'error', 'message' => 'Invalid card credentials'], 401);
                return;
            }
        }

        return $this->handleInvalidEndpoint();
    }

    private function handleInvalidEndpoint()
    {
        $this->sendResponse(['status' => 'error', 'message' => 'Invalid endpoint'], 404);
    }

    private function sendResponse($response, $statusCode = 200)
    {
        header('HTTP/1.1 ' . $statusCode . ' ' . $this->getStatusCodeMessage($statusCode));
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        echo json_encode($response);
        exit;
    }

    private function getStatusCodeMessage($statusCode)
    {
        $codes = array(
            200 => 'OK',
            201 => 'Created',
            204 => 'No Content',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
        );

        return (isset($codes[$statusCode])) ? $codes[$statusCode] : '';
    }

    private function getItemList($seller = 'seller1')
    {
        $itemService = new ItemService($this->db);
        $items = $itemService->getItemsForSeller($seller);

        if ($items) {
            $this->sendResponse(
                ['success' => true, 'data' => $items, 'seller_ip' => 'seller1' === $seller ? '127.0.0.1:8080' : '127.0.0.1:8000'],
                200
            );
        } else {
            $this->sendResponse(['success' => false, 'message' => 'No items found.'], 404);
        }
    }

    private function searchForItems()
    {
        $searchTerm = $_GET['search'] ?? null;
        if (!$searchTerm) {
            $this->sendResponse(['success' => false, 'message' => 'Search term not provided.'], 400);
            return;
        }
        $itemSearchService = new ItemSearchService();
        $results = $itemSearchService->searchItems($searchTerm);

        if ($results) {
            $this->sendResponse(['success' => true, 'items' => $results], 200);
        } else {
            $this->sendResponse(['success' => false, 'message' => 'No items found for the given search term.'], 404);
        }
    }

    private function purchaseItem()
    {
        $itemId = $this->data['itemId'] ?? 0;
        $userId = $this->data['userId'] ?? 0;
        $quantity = $this->data['quantity'] ?? 1;
        $seller = $this->data['seller_ip'] ?? null;

        $purchaseService = new PurchaseService($this->db);

        $result = $purchaseService->makePurchase($userId, $itemId, $quantity, $seller);

        if (-2 === $result) {
            $this->sendResponse(['success' => false, 'message' => 'Item not found.'], 400);
        } else if (false === $result) {
            $this->sendResponse(['success' => false, 'message' => 'User not found.'], 400);
        } else if (-1 === $result) {
            $this->sendResponse(['success' => false, 'message' => 'Insufficient balance.'], 400);
        } else if (null === $result) {
            $this->sendResponse(['success' => false, 'message' => 'Sorry!, error while creating purchasing.'], 500);
        }

        $this->sendResponse(['success' => true, 'message' => 'Purchase completed successfully!'], 201);
    }


    private function searchPurchase()
    {
        $purId = $_GET['purId'] ?? 0;
        $userId = $_GET['userId'] ?? 0;

        $purchaseService = new PurchaseService($this->db);

        if ($purId) {
            $result = $purchaseService->getPurchaseDetails($purId);
            if ($result) {
                $this->sendResponse(['success' => true, 'data' => $result], 200);
            } else {
                $this->sendResponse(['success' => false, 'message' => 'No purchase found for the provided purId.'], 404);
            }
        } elseif ($userId) {
            $result = $purchaseService->getPurchasesByUserId($userId);
            if ($result) {
                $this->sendResponse(['success' => true, 'data' => $result], 200);
            } else {
                $this->sendResponse(['success' => false, 'message' => 'No purchases found for the provided userId.'], 404);
            }
        } else {
            $this->sendResponse(['success' => false, 'message' => 'Neither purId nor userId provided.'], 400);
        }
    }

    private function cancelPurchase()
    {
        $purchaseId = $this->data['purchaseId'] ?? 0;
        $purchaseService = new PurchaseService($this->db);

        if ($purchaseService->cancelPurchase($purchaseId)) {
            $this->sendResponse(['success' => true, 'message' => 'Purchase cancelled successfully.'], 201);
        } else {
            $this->sendResponse(['success' => false, 'message' => 'Failed to cancel the purchase.'], 500);
        }
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
