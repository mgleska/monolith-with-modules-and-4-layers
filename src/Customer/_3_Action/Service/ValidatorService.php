<?php

declare(strict_types=1);

namespace App\Customer\_3_Action\Service;

use App\Customer\_2_Export\ValidateIdInterface;
use App\Customer\_4_Infrastructure\Repository\CustomerRepository;

class ValidatorService implements ValidateIdInterface
{
    public function __construct(
        private readonly CustomerRepository $repository,
    ) {
    }

    public function isIdValid(int $id): bool
    {
        if ($this->repository->count(["id" => $id]) === 0) {
            return false;
        }

        return true;
    }
}
