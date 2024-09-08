<?php

declare(strict_types=1);

namespace App\Customer\_2_Export;

use App\Api\_2_Export\ApiProblemException;

interface ValidateIdInterface
{
    /**
     * @throws ApiProblemException
     */
    public function isIdValid(int $id): bool;
}
