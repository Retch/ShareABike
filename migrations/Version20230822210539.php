<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230822210539 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lock ADD latitude_degrees DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE lock ADD longitude_degrees DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE lock ADD latitude_hemisphere VARCHAR(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE lock ADD longitude_hemisphere VARCHAR(1) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE lock DROP latitude_degrees');
        $this->addSql('ALTER TABLE lock DROP longitude_degrees');
        $this->addSql('ALTER TABLE lock DROP latitude_hemisphere');
        $this->addSql('ALTER TABLE lock DROP longitude_hemisphere');
    }
}
