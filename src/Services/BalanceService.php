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
            return false;
        }

        $result = $this->userModel->updateUserBalance($userId, $amount);

        if ($result) {
            return $result;
        } else {
            return null;
        }
    }
}
