<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241019172259 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ord_order ADD version INT NOT NULL');
        $this->addSql('UPDATE ord_order SET version = 1 WHERE version = 0');
        $this->addSql('ALTER TABLE ord_fixed_address ADD version INT NOT NULL');
        $this->addSql('UPDATE ord_fixed_address SET version = 1 WHERE version = 0');
        $this->addSql('ALTER TABLE cst_customer ADD version INT NOT NULL');
        $this->addSql('UPDATE cst_customer SET version = 1 WHERE version = 0');
        $this->addSql('ALTER TABLE auth_user ADD version INT NOT NULL');
        $this->addSql('UPDATE auth_user SET version = 1 WHERE version = 0');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ord_order DROP version');
        $this->addSql('ALTER TABLE ord_fixed_address DROP version');
        $this->addSql('ALTER TABLE cst_customer DROP version');
        $this->addSql('ALTER TABLE auth_user DROP version');
    }
}
