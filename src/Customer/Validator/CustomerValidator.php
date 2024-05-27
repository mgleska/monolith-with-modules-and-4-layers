<?php

declare(strict_types=1);

namespace App\Customer\Validator;

use App\Api\Export\ApiProblemException;
use App\Customer\Enum\ApiProblemTypeEnum;
use App\Customer\Export\ValidateIdInterface;
use App\Customer\Repository\CustomerRepository;
use Symfony\Component\HttpFoundation\Response;

class CustomerValidator implements ValidateIdInterface
{
    public function __construct(
        private readonly CustomerRepository $repository,
    ) {
    }

    /**
     * @throws ApiProblemException
     */
    public function validateId(int $id): void
    {
        if ($this->repository->count(["id" => $id]) === 0) {
            throw new ApiProblemException(
                Response::HTTP_NOT_FOUND,
                ApiProblemTypeEnum::VALIDATOR->value,
                'CUSTOMER_ID_NOT_FOUND'
            );
        }
    }
}
