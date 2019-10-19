<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191019220450 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE subcategory_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE subcategory (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE user_category (user_id INT NOT NULL, category_id INT NOT NULL, PRIMARY KEY(user_id, category_id))');
        $this->addSql('CREATE INDEX IDX_E6C1FDC1A76ED395 ON user_category (user_id)');
        $this->addSql('CREATE INDEX IDX_E6C1FDC112469DE2 ON user_category (category_id)');
        $this->addSql('CREATE TABLE user_subcategory (user_id INT NOT NULL, subcategory_id INT NOT NULL, PRIMARY KEY(user_id, subcategory_id))');
        $this->addSql('CREATE INDEX IDX_A4C76DDDA76ED395 ON user_subcategory (user_id)');
        $this->addSql('CREATE INDEX IDX_A4C76DDD5DC6FE57 ON user_subcategory (subcategory_id)');
        $this->addSql('ALTER TABLE user_category ADD CONSTRAINT FK_E6C1FDC1A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_category ADD CONSTRAINT FK_E6C1FDC112469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_subcategory ADD CONSTRAINT FK_A4C76DDDA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_subcategory ADD CONSTRAINT FK_A4C76DDD5DC6FE57 FOREIGN KEY (subcategory_id) REFERENCES subcategory (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE fos_user ADD percentages TEXT DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN fos_user.percentages IS \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE category ADD name VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE user_subcategory DROP CONSTRAINT FK_A4C76DDD5DC6FE57');
        $this->addSql('DROP SEQUENCE subcategory_id_seq CASCADE');
        $this->addSql('DROP TABLE subcategory');
        $this->addSql('DROP TABLE user_category');
        $this->addSql('DROP TABLE user_subcategory');
        $this->addSql('ALTER TABLE category DROP name');
        $this->addSql('ALTER TABLE fos_user DROP percentages');
    }
}
