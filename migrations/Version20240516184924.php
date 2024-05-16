<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240516184924 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ord_order (id INT AUTO_INCREMENT NOT NULL, customer_id INT NOT NULL, number VARCHAR(50) NOT NULL, status VARCHAR(20) NOT NULL, quantity_total INT NOT NULL, loading_name_company_or_person VARCHAR(250) NOT NULL, loading_address VARCHAR(250) NOT NULL, loading_city VARCHAR(250) NOT NULL, loading_zip_code VARCHAR(50) NOT NULL, loading_contact_person VARCHAR(250) NOT NULL, loading_contact_phone VARCHAR(250) NOT NULL, loading_contact_email VARCHAR(250) DEFAULT NULL, delivery_name_company_or_person VARCHAR(250) NOT NULL, delivery_address VARCHAR(250) NOT NULL, delivery_city VARCHAR(250) NOT NULL, delivery_zip_code VARCHAR(50) NOT NULL, delivery_contact_person VARCHAR(250) NOT NULL, delivery_contact_phone VARCHAR(250) NOT NULL, delivery_contact_email VARCHAR(250) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ord_order_line (id INT AUTO_INCREMENT NOT NULL, order_id INT DEFAULT NULL, customer_id INT NOT NULL, quantity INT NOT NULL, length INT NOT NULL, width INT NOT NULL, height INT NOT NULL, weight_one_pallet INT NOT NULL, weight_total INT NOT NULL, goods_description VARCHAR(250) NOT NULL, INDEX IDX_6E2713D58D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE ord_order_sscc (id INT AUTO_INCREMENT NOT NULL, order_id INT DEFAULT NULL, customer_id INT NOT NULL, code BIGINT NOT NULL, INDEX IDX_2B21A9C28D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ord_order_line ADD CONSTRAINT FK_6E2713D58D9F6D38 FOREIGN KEY (order_id) REFERENCES ord_order (id)');
        $this->addSql('ALTER TABLE ord_order_sscc ADD CONSTRAINT FK_2B21A9C28D9F6D38 FOREIGN KEY (order_id) REFERENCES ord_order (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ord_order_line DROP FOREIGN KEY FK_6E2713D58D9F6D38');
        $this->addSql('ALTER TABLE ord_order_sscc DROP FOREIGN KEY FK_2B21A9C28D9F6D38');
        $this->addSql('DROP TABLE ord_order');
        $this->addSql('DROP TABLE ord_order_line');
        $this->addSql('DROP TABLE ord_order_sscc');
    }
}
