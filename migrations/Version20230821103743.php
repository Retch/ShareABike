<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230821103743 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lock ADD battery_percentage SMALLINT DEFAULT NULL');
        $this->addSql('ALTER TABLE lock_type ADD battery_voltage_min DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE lock_type ADD battery_voltage_max DOUBLE PRECISION NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE lock_type DROP battery_voltage_min');
        $this->addSql('ALTER TABLE lock_type DROP battery_voltage_max');
        $this->addSql('ALTER TABLE lock DROP battery_percentage');
    }
}
