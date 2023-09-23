<?php

require '../vendor/autoload.php';

use Marketplace\Database\Connection;

class App
{
    private $db;
    private $requestMethod;
    private $pathInfo;
    private $data;

    public function __construct()
    {
        $this->redirectToHttps();
        $this->db = Connection::getInstance();
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
        $this->pathInfo = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->data = $this->getRequestData();
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
        switch ($this->pathInfo) {
            case '/test':
                return $this->handleTestEndpoint();
                // case '/seller1/itemlist.php':
                //     return $this->getItemList();
                // case '/search-items':
                //     return $this->searchForItems();
                // case '/purchase':
                //     return $this->purchaseItem();
                // case '/search-purchase':
                //     return $this->searchPurchase();
                // case '/cancel-purchase':
                //     return $this->cancelPurchase();
                // case '/add-balance':
                //     return $this->addBalance();
                // case '/card-check':
                //     return $this->checkCard();
            default:
                return $this->handleInvalidEndpoint();
        }
    }

    private function handleTestEndpoint()
    {
        if ($this->requestMethod === 'GET') {
            return ['status' => 'success', 'message' => 'Tested Successfully.'];
        } else {
            header('HTTP/1.1 405 Method Not Allowed');
            return ['status' => 'error', 'message' => 'Method not allowed'];
        }
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
}

$app = new App();
$app->run();
