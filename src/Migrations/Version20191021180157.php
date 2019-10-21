<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191021180157 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE fos_user ADD picture VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE fos_user ADD messenger VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE fos_user DROP facebook_id');
        $this->addSql('ALTER TABLE fos_user DROP facebook_access_token');
        $this->addSql('ALTER TABLE fos_user DROP last_name');
        $this->addSql('ALTER TABLE fos_user DROP picture_url');
        $this->addSql('ALTER TABLE fos_user ALTER birthday TYPE TIMESTAMP(0) USING birthday::timestamp(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE fos_user ALTER birthday DROP DEFAULT');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE fos_user ADD facebook_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE fos_user ADD facebook_access_token VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE fos_user ADD last_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE fos_user ADD picture_url VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE fos_user DROP picture');
        $this->addSql('ALTER TABLE fos_user DROP messenger');
        $this->addSql('ALTER TABLE fos_user ALTER birthday TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE fos_user ALTER birthday DROP DEFAULT');
    }
}
