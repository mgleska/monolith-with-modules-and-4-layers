<?php

declare(strict_types=1);

namespace App\Customer\_2_Export;

use App\CommonInfrastructure\Api\ApiProblemException;

interface ValidateIdInterface
{
    /**
     * @throws ApiProblemException
     */
    public function isIdValid(int $id): bool;
}
