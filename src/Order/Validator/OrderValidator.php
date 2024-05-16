<?php

declare(strict_types=1);

namespace App\Order\Validator;

use App\Api\Export\Exception\ApiProblemException;
use App\Order\Api\ApiProblemType;
use App\Order\Entity\Order;
use Symfony\Component\HttpFoundation\Response;

class OrderValidator
{
    public function validateExists(?Order $order): void
    {
        if ($order === null) {
            throw new ApiProblemException(
                Response::HTTP_NOT_FOUND,
                ApiProblemType::VALIDATOR->value,
                'ORDER_ORDER_NOT_FOUND'
            );
        }
    }
}
