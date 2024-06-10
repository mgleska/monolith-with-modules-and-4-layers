<?php

declare(strict_types=1);

namespace App\Order\_3_Action\Validator;

use App\Api\_2_Export\ApiProblemException;
use App\Customer\_2_Export\ValidateIdInterface;
use App\Order\_3_Action\Enum\ApiProblemTypeEnum;
use Symfony\Component\HttpFoundation\Response;

class CustomerValidator
{
    public function __construct(
        private readonly ValidateIdInterface $idValidator,
    ) {
    }

    public function validateCustomerId(int $id): void
    {
        if (! $this->idValidator->isIdValid($id)) {
            throw new ApiProblemException(
                Response::HTTP_NOT_FOUND,
                ApiProblemTypeEnum::VALIDATOR->value,
                'CUSTOMER_ID_NOT_FOUND'
            );
        }
    }
}
