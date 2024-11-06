<?php

declare(strict_types=1);

namespace App\Admin\_2_Export;

use Doctrine\DBAL\Exception as DBALException;

interface SwitchDatabaseInterface
{
    /**
     * @throws DBALException
     */
    public function switchDatabase(): void;
}
