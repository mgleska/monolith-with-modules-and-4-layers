<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241006161500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('RENAME TABLE ord_order TO ord_order_header;');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('RENAME TABLE ord_order_header TO ord_order;');
    }
}
