<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241104174508 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cst_customer ADD db_name_suffix VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE ord_fixed_address DROP customer_id');
        $this->addSql('ALTER TABLE ord_order DROP customer_id');
        $this->addSql('ALTER TABLE ord_order_line DROP customer_id');
        $this->addSql('ALTER TABLE ord_order_sscc DROP customer_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cst_customer DROP db_name_suffix');
        $this->addSql('ALTER TABLE ord_order_line ADD customer_id INT NOT NULL');
        $this->addSql('ALTER TABLE ord_order_sscc ADD customer_id INT NOT NULL');
        $this->addSql('ALTER TABLE ord_order ADD customer_id INT NOT NULL');
        $this->addSql('ALTER TABLE ord_fixed_address ADD customer_id INT NOT NULL');
    }
}
