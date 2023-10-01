<?php

namespace Marketplace\Services;

use Marketplace\Models\ItemModel;
use Marketplace\Models\PurchaseModel;
use Marketplace\Models\UserModel;

class PurchaseService
{
    private $purchaseModel;
    private $userModel;
    private $itemModel;

    public function __construct($conn)
    {
        $this->purchaseModel = new PurchaseModel($conn);
        $this->userModel = new UserModel($conn);
        $this->itemModel = new ItemModel($conn);
    }

    public function makePurchase($userId, $itemId, $quantity, $seller)
    {
        $item = $this->itemModel->getItemById((int)$itemId, $seller);

        if (!$item) {
            return -2;
        }

        $totalPrice = $item['price_of_unit'] *  $quantity;
        $formattedPrice = number_format($totalPrice, 2, '.', '');

        $user = $this->userModel->getUser((int)$userId);

        if (!$user) {
            return false;
        }

        if ($user['balance'] < $totalPrice) {
            return -1;
        }

        $purchaseData = [
            'user_id' => $userId,
            'item_id' => $itemId,
            'quantity' => $quantity,
            'price' => $formattedPrice,
            'seller_ip' => $seller
        ];

        $purId = $this->purchaseModel->insertPurchase($purchaseData);

        if (!$purId) {
            return null;
        }

        $this->userModel->updateUserBalance($userId, -$totalPrice);

        return true;
    }

    public function getPurchasesByUserId($userId)
    {
        $purchases = $this->purchaseModel->getPurchasesForUser($userId);

        return $purchases ?? array();
    }

    public function getPurchaseDetails($purId)
    {
        $purchase = $this->purchaseModel->getPurchase($purId);

        return $purchase ? array($purchase) : array();
    }


    public function cancelPurchase($purId)
    {
        $deletedRows = $this->purchaseModel->deletePurchase($purId);

        if (!$deletedRows) {
            return false;
        }

        return true;
    }
}
