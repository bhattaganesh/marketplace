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
        $item = $this->itemModel->getItemById($itemId, $seller);

        if (!$item) {
            return ['success' => false, 'message' => 'Item not found.'];
        }

        $totalPrice = $item['price'] * $quantity;

        $user = $this->userModel->getUser($userId);
        if ($user['balance'] < $totalPrice) {
            return ['success' => false, 'message' => 'Insufficient balance.'];
        }

        $purchaseData = [
            'user_id' => $userId,
            'item_id' => $itemId,
            'quantity' => $quantity,
            'price' => $totalPrice,
            'seller_id' => $seller
        ];

        $this->purchaseModel->insertPurchase($purchaseData);
        $this->userModel->updateUserBalance($userId, -$totalPrice);

        return ['success' => true, 'message' => 'Purchase completed successfully!'];
    }

    public function getPurchaseDetails($purId)
    {
        return $this->purchaseModel->getPurchase($purId);
    }

    public function removePurchase($purId)
    {
        return $this->purchaseModel->deletePurchase($purId);
    }

    public function findPurchasesByUser($userId)
    {
    }

    public function cancelPurchase($purId)
    {
    }
}
