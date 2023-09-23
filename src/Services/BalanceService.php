<?php

namespace Marketplace\Services;

use Marketplace\Models\UserModel;


class BalanceService
{
    private $userModel;

    public function __construct($conn)
    {
        $this->userModel = new UserModel($conn);
    }

    public function addUserBalance($userId, $amount)
    {
        $user = $this->userModel->getUser($userId);

        if (!$user) {
            return ['success' => false, 'message' => 'User not found.'];
        }

        $result = $this->userModel->updateUserBalance($userId, $amount);

        if ($result) {
            return ['success' => true, 'message' => 'Balance updated successfully.'];
        } else {
            return ['success' => false, 'message' => 'Failed to update balance.'];
        }
    }
}
