<?php

declare(strict_types=1);

namespace App\Auth\_2_Export;

use App\CommonInfrastructure\Api\ApiProblemException;

interface RoleValidatorInterface
{
    /**
     * @throws ApiProblemException
     */
    public function validateHasRole(string $role): void;
}
