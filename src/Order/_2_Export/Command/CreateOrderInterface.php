<?php

declare(strict_types=1);

namespace App\Order\_2_Export\Command;

use App\Order\_2_Export\Dto\Order\CreateOrderDto;
use Exception;

interface CreateOrderInterface
{
    /**
     * @throws Exception
     */
    public function createOrder(CreateOrderDto $dto, bool $isValidated = false): int;
}
