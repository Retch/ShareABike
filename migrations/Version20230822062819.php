<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230822062819 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lock ADD last_position_time TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE lock ADD cellular_signal_quality_percentage SMALLINT DEFAULT NULL');
        $this->addSql('ALTER TABLE lock ADD satellites SMALLINT DEFAULT NULL');
        $this->addSql('ALTER TABLE lock_type ADD cellular_signal_quality_min SMALLINT DEFAULT NULL');
        $this->addSql('ALTER TABLE lock_type ADD cellular_signal_quality_max SMALLINT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE lock DROP last_position_time');
        $this->addSql('ALTER TABLE lock DROP cellular_signal_quality_percentage');
        $this->addSql('ALTER TABLE lock DROP satellites');
        $this->addSql('ALTER TABLE lock_type DROP cellular_signal_quality_min');
        $this->addSql('ALTER TABLE lock_type DROP cellular_signal_quality_max');
    }
}
