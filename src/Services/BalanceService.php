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
        return $this->userModel->updateUserBalance($userId, $amount);
    }
}
