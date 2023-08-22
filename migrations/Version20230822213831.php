<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230822213831 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lock ADD last_position_hdop DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE lock ADD last_position_altitude_meters DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE lock ADD bluetooth_mac VARCHAR(17) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE lock DROP last_position_hdop');
        $this->addSql('ALTER TABLE lock DROP last_position_altitude_meters');
        $this->addSql('ALTER TABLE lock DROP bluetooth_mac');
    }
}
