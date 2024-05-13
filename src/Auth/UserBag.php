<?php

declare(strict_types=1);

namespace App\Auth;

use App\Auth\Export\UserBag as UserBagInterface;

class UserBag implements UserBagInterface
{
    private int $userId;
    private int $customerId;

    public function __construct()
    {
        $this->userId = 1;
        $this->customerId = 11;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }
}
