<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230822213113 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lock ADD info_sw_version VARCHAR(31) DEFAULT NULL');
        $this->addSql('ALTER TABLE lock ADD info_sw_date DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE lock ADD info_hw_revision VARCHAR(31) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE lock DROP info_sw_version');
        $this->addSql('ALTER TABLE lock DROP info_sw_date');
        $this->addSql('ALTER TABLE lock DROP info_hw_revision');
    }
}
