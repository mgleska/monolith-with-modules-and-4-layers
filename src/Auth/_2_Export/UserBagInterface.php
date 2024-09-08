<?php

declare(strict_types=1);

namespace App\Auth\_2_Export;

interface UserBagInterface
{
    public function getUserId(): int;
    public function getCustomerId(): int;
}
