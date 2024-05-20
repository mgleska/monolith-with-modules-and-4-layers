<?php

declare(strict_types=1);

namespace App\Order\Export\Dto\Order;

use Symfony\Component\Validator\Constraints as Assert;

class PrintLabelDto
{
    #[Assert\Range(min: 1)]
    public int $orderId;
}