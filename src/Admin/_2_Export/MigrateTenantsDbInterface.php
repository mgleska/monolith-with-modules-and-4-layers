<?php

declare(strict_types=1);

namespace App\Admin\_2_Export;

use Doctrine\DBAL\Exception as DBALException;
use Symfony\Component\Console\Style\SymfonyStyle;

interface MigrateTenantsDbInterface
{
    /**
     * @throws DBALException
     */
    public function migrateTenantsDb(SymfonyStyle $io): void;
}
