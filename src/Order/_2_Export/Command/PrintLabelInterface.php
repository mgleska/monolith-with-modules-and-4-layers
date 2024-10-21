<?php

declare(strict_types=1);

namespace App\Order\_2_Export\Command;

use Exception;

interface PrintLabelInterface
{
    /**
     * @return array{bool, string}
     * @throws Exception
     */
    public function printLabel(int $orderId): array;
}
