<?php

declare(strict_types=1);

namespace App\Admin\_2_Export;

use Doctrine\DBAL\Exception as DBALException;

interface InitCommonDbInterface
{
    /**
     * @throws DBALException
     */
    public function initCommonDb(): void;
}
