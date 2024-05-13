<?php

declare(strict_types=1);

namespace App\Auth\Export;

interface UserBag
{
    public function getUserId(): int;
    public function getCustomerId(): int;
}
