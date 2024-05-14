<?php

declare(strict_types=1);

namespace App\Admin\CliCommand;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'admin:init',
    description: 'Load initial database content.',
)]
class InitCommand extends Command
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        parent::__construct();
        $this->connection = $connection;
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->ex('TRUNCATE TABLE cst_customer');
        $this->ex("INSERT INTO cst_customer (id, name) VALUES(1, 'System owner')");
        $this->ex("INSERT INTO cst_customer (id, name) VALUES(2, 'Acme Company')");

        $this->ex('TRUNCATE TABLE auth_user');
        $this->ex("INSERT INTO auth_user (id, login, customer_id, roles) VALUES(NULL, 'admin', 1, JSON_ARRAY(\"ROLE_ADMIN\", \"ROLE_USER\"))");
        $this->ex("INSERT INTO auth_user (id, login, customer_id, roles) VALUES(NULL, 'user-1', 2, JSON_ARRAY(\"ROLE_USER\"))");

        $this->ex('TRUNCATE TABLE ord_fixed_address');
        $this->ex("
            INSERT INTO ord_fixed_address (id, customer_id, external_id, name_company_or_person, address, city, zip_code) 
            VALUES(1, 2, 'HQ', 'Acme Company', 'ul. Garbary 125', 'Poznań', '61-719')");

        $io->success('Database populated with initial data.');

        return Command::SUCCESS;
    }

    /**
     * @throws Exception
     */
    private function ex(string $q): void
    {
        $this->connection->executeStatement($q);
    }
}
