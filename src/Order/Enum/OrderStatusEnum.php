<?php

declare(strict_types=1);

namespace App\Order\Enum;

enum OrderStatusEnum: string
{
    case NEW = 'NEW';
    case SENT = 'SENT';
    case CONFIRMED = 'CONFIRMED';
    case DELIVERED = 'DELIVERED';
    case CANCELLED = 'CANCELLED';
}
