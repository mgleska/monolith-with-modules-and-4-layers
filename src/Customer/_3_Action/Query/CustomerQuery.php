<?php

declare(strict_types=1);

namespace App\Customer\_3_Action\Query;

use App\CommonInfrastructure\Api\ApiProblemException;
use App\Customer\_2_Export\Dto\CustomerDto;
use App\Customer\_2_Export\GetCustomerInterface;
use App\Customer\_3_Action\Validator\CustomerValidator;
use App\Customer\_4_Infrastructure\Repository\CustomerRepository;

class CustomerQuery implements GetCustomerInterface
{
    public function __construct(
        private readonly CustomerRepository $repository,
        private readonly CustomerValidator $validator,
    ) {
    }

    /**
     * @throws ApiProblemException
     */
    public function getCustomer(int $id): CustomerDto
    {
        $customer = $this->repository->findById($id);
        $this->validator->validateExists($customer);

        return CustomerDto::fromEntity($customer);
    }
}
