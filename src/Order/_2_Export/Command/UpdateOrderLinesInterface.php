<?php

declare(strict_types=1);

namespace App\Order\_2_Export\Command;

use App\Order\_2_Export\Dto\Order\UpdateOrderLinesDto;

interface UpdateOrderLinesInterface
{
    /**
     * @return array{bool, string}
     */
    public function updateOrderLines(UpdateOrderLinesDto $dto): array;
}
