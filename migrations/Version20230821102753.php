<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230821102753 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lock ADD lock_type_id INT NOT NULL');
        $this->addSql('ALTER TABLE lock ADD CONSTRAINT FK_878F9B0EA5619795 FOREIGN KEY (lock_type_id) REFERENCES lock_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_878F9B0EA5619795 ON lock (lock_type_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE lock DROP CONSTRAINT FK_878F9B0EA5619795');
        $this->addSql('DROP INDEX IDX_878F9B0EA5619795');
        $this->addSql('ALTER TABLE lock DROP lock_type_id');
    }
}
