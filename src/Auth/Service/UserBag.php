<?php

declare(strict_types=1);

namespace App\Auth\Service;

use App\Auth\Export\UserBag as UserBagInterface;

class UserBag implements UserBagInterface
{
    private int $userId;
    private int $customerId;

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function setCustomerId(int $customerId): void
    {
        $this->customerId = $customerId;
    }
}
