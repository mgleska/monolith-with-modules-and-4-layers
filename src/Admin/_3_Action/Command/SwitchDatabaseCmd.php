<?php

declare(strict_types=1);

namespace App\Admin\_3_Action\Command;

use App\Admin\_2_Export\SwitchDatabaseInterface;
use App\CommonInfrastructure\DatabaseService;
use App\Customer\_2_Export\CustomerBagInterface;
use Doctrine\DBAL\Exception as DBALException;

class SwitchDatabaseCmd implements SwitchDatabaseInterface
{
    public function __construct(
        private readonly DatabaseService $databaseService,
        private readonly CustomerBagInterface $customerBag,
    ) {
    }

    /**
     * @throws DBALException
     */
    public function switchDatabase(): void
    {
        $this->databaseService->switchDatabase($this->customerBag->getDatabaseSuffix());
    }
}
