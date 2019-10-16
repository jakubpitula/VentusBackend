<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191014124434 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE fos_user ADD first_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE fos_user ADD last_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE fos_user ADD gender VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE fos_user ADD picture_url VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE fos_user ADD age INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE fos_user DROP first_name');
        $this->addSql('ALTER TABLE fos_user DROP last_name');
        $this->addSql('ALTER TABLE fos_user DROP gender');
        $this->addSql('ALTER TABLE fos_user DROP picture_url');
        $this->addSql('ALTER TABLE fos_user DROP age');
    }
}
