<?php

declare(strict_types=1);

namespace App\Order\Export\Dto\Order;

use Symfony\Component\Validator\Constraints as Assert;

class SendOrderDto
{
    #[Assert\Range(min: 1)]
    #[Assert\NotNull]
    public int $orderId;
}
