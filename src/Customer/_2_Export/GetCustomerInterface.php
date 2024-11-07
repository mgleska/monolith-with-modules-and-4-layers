<?php

declare(strict_types=1);

namespace App\Customer\_2_Export;

use App\CommonInfrastructure\Api\ApiProblemException;
use App\Customer\_2_Export\Dto\CustomerDto;

interface GetCustomerInterface
{
    /**
     * @throws ApiProblemException
     */
    public function getCustomer(int $id): CustomerDto;
}
