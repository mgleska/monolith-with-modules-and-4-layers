<?php

declare(strict_types=1);

namespace App\Order\_2_Export\Query;

use App\Order\_2_Export\Dto\FixedAddress\FixedAddressDto;

interface GetAllFixedAddressesInterface
{
    /**
     * @return FixedAddressDto[]
     */
    public function getAllFixedAddresses(): array;
}
