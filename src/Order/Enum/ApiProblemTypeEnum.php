<?php

declare(strict_types=1);

namespace App\Order\Enum;

enum ApiProblemTypeEnum: string
{
    case VALIDATOR = 'order/validator';
    case CREATE = 'order/create';
}
