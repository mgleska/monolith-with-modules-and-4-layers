<?php

declare(strict_types=1);

namespace App\Order\_2_Export\Command;

use Doctrine\DBAL\Exception as DBALException;
use Exception;

interface PrintLabelInterface
{
    /**
     * @return array{bool, string}
     * @throws DBALException
     * @throws Exception
     */
    public function printLabel(int $orderId): array;
}
