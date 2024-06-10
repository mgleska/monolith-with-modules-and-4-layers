<?php

declare(strict_types=1);

namespace App\Order\_2_Export\Query;

use App\Order\_2_Export\Dto\Order\OrderDto;

interface GetOrderInterface
{
    public function getOrder(int $id): OrderDto;
}
