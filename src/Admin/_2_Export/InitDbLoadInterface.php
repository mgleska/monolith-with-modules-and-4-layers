<?php

declare(strict_types=1);

namespace App\Admin\_2_Export;

use Doctrine\DBAL\Exception as DBALException;

interface InitDbLoadInterface
{
    /**
     * @throws DBALException
     */
    public function initDbLoad(): void;
}
