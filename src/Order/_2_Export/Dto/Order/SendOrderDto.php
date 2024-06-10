<?php

declare(strict_types=1);

namespace App\Order\_2_Export\Dto\Order;

use Symfony\Component\Validator\Constraints as Assert;

class SendOrderDto
{
    #[Assert\Range(min: 1)]
    public readonly int $orderId;

    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }
}
