<?php

declare(strict_types=1);

namespace App\Customer\_2_Export;

interface CustomerBagInterface
{
    public function getCustomerId(): int;
    public function getName(): string;
    public function getDatabaseSuffix(): string;
}
