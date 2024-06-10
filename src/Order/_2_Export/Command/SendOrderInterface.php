<?php

declare(strict_types=1);

namespace App\Order\_2_Export\Command;

use Doctrine\DBAL\Exception as DBALException;

interface SendOrderInterface
{
    /**
     * @return array{bool, string}
     * @throws DBALException
     */
    public function sendOrder(int $orderId): array;
}
