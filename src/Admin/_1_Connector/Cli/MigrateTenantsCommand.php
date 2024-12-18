<?php

declare(strict_types=1);

namespace App\Admin\_1_Connector\Cli;

use App\Admin\_2_Export\MigrateTenantsDbInterface;
use Doctrine\DBAL\Exception as DBALException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'admin:migrate-tenants',
    description: 'Execute Doctrine migrations for all tenants.',
)]
class MigrateTenantsCommand extends Command
{
    private MigrateTenantsDbInterface $service;

    public function __construct(MigrateTenantsDbInterface $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    /**
     * @throws DBALException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $this->service->migrateTenantsDb($io);
        $io->success('Executed Doctrine migrations for tenants databases.');

        return Command::SUCCESS;
    }
}
