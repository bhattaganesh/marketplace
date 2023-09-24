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

        $totalPrice = $item['price_of_unit'] *  $quantity;
        $formattedPrice = number_format($totalPrice, 2, '.', '');

        $user = $this->userModel->getUser($userId);

        if (!$user) {
            return ['success' => false, 'message' => 'User not found.'];
        }

        if ($user['balance'] < $totalPrice) {
            return ['success' => false, 'message' => 'Insufficient balance.'];
        }

        $purchaseData = [
            'user_id' => $userId,
            'item_id' => $itemId,
            'quantity' => $quantity,
            'price' => $formattedPrice,
            'seller_id' => $seller
        ];

        $purId = $this->purchaseModel->insertPurchase($purchaseData);

        if (!$purId) {
            return ['success' => false, 'message' => 'Sorry!, error while creating purchasing.'];
        }

        $this->userModel->updateUserBalance($userId, -$totalPrice);

        return ['success' => true, 'message' => 'Purchase completed successfully!'];
    }

    public function getPurchasesByUserId($userId)
    {
        $purchases = $this->purchaseModel->getPurchasesForUser($userId);

        if (!$purchases) {
            return ['success' => false, 'message' => 'No purchases found for the user.'];
        }

        return ['success' => true, 'data' => $purchases];
    }

    public function getPurchaseDetails($purId)
    {
        $purchase = $this->purchaseModel->getPurchase($purId);

        if (!$purchase) {
            return ['success' => false, 'message' => 'Purchase not found.'];
        }

        return ['success' => true, 'data' => array($purchase)];
    }


    public function cancelPurchase($purId)
    {
        $deletedRows = $this->purchaseModel->deletePurchase($purId);

        if (!$deletedRows) {
            return ['success' => false, 'message' => 'Failed to cancel the purchase or purchase not found.'];
        }

        return ['success' => true, 'message' => 'Purchase cancelled successfully.'];
    }
}
