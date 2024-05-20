<?php

declare(strict_types=1);

namespace App\Customer\Api;

enum ApiProblemTypeEnum: string
{
    case VALIDATOR = 'customer/validator';
}
