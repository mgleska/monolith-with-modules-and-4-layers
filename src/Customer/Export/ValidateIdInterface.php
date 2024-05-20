<?php

declare(strict_types=1);

namespace App\Customer\Export;

use App\Api\Export\ApiProblemException;

interface ValidateIdInterface
{
    /**
     * @throws ApiProblemException
     */
    public function validateId(int $id): void;
}
