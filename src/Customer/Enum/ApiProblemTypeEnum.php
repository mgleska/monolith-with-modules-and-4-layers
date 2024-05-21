<?php

declare(strict_types=1);

namespace App\Customer\Enum;

enum ApiProblemTypeEnum: string
{
    case VALIDATOR = 'customer/validator';
}
