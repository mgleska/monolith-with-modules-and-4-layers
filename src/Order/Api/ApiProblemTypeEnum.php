<?php

declare(strict_types=1);

namespace App\Order\Api;

enum ApiProblemTypeEnum: string
{
    case VALIDATOR = 'order/validator';
}
