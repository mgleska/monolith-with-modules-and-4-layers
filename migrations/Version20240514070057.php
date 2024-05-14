<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240514070057 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE auth_user ADD roles JSON NOT NULL COMMENT \'(DC2Type:json)\', CHANGE login login VARCHAR(180) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_LOGIN ON auth_user (login)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_IDENTIFIER_LOGIN ON auth_user');
        $this->addSql('ALTER TABLE auth_user DROP roles, CHANGE login login VARCHAR(50) NOT NULL');
    }
}
