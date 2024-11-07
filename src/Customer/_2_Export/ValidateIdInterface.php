<?php

declare(strict_types=1);

namespace App\Customer\_2_Export;

use Doctrine\DBAL\Exception as DBALException;

interface ValidateIdInterface
{
    /**
     * @throws DBALException
     */
    public function isIdValid(int $id): bool;
}
