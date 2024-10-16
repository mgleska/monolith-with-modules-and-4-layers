<?php

declare(strict_types=1);

namespace App\Order\_2_Export\Command;

use App\Order\_2_Export\Dto\FixedAddress\CreateFixedAddressDto;

interface CreateFixedAddressInterface
{
    public function createFixedAddress(CreateFixedAddressDto $dto): int;
}
