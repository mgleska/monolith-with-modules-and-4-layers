<?php

declare(strict_types=1);

namespace App\Customer\_3_Action\Validator;

use App\CommonInfrastructure\Api\ApiProblemException;
use App\Customer\_3_Action\Entity\Customer;
use App\Customer\_3_Action\Enum\ApiProblemTypeEnum;
use Symfony\Component\HttpFoundation\Response;

class CustomerValidator
{
    /**
     * @throws ApiProblemException
     */
    public function validateExists(?Customer $customer): void
    {
        if ($customer === null) {
            throw new ApiProblemException(
                Response::HTTP_NOT_FOUND,
                ApiProblemTypeEnum::VALIDATOR->value,
                'CUSTOMER_CUSTOMER_NOT_FOUND'
            );
        }
    }
}
