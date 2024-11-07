<?php

declare(strict_types=1);

namespace App\Customer\_3_Action\Service;

use App\Customer\_2_Export\ValidateIdInterface;
use App\Customer\_4_Infrastructure\Repository\CustomerRepository;
use Doctrine\DBAL\Exception as DBALException;

class ValidatorService implements ValidateIdInterface
{
    public function __construct(
        private readonly CustomerRepository $repository,
    ) {
    }

    /**
     * @throws DBALException
     */
    public function isIdValid(int $id): bool
    {
        return $this->repository->checkId($id);
    }
}
