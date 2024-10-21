<?php

declare(strict_types=1);

namespace App\Order\_2_Export\Command;

use Exception;

interface SendOrderInterface
{
    /**
     * @return array{bool, string}
     * @throws Exception
     */
    public function sendOrder(int $orderId, int $version): array;
}
