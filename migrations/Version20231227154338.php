<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231227154338 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE trip_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE trip (id INT NOT NULL, customer_id INT NOT NULL, bike_id INT DEFAULT NULL, start_time TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, time_end TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7656F53B9395C3F3 ON trip (customer_id)');
        $this->addSql('CREATE INDEX IDX_7656F53BD5A4816F ON trip (bike_id)');
        $this->addSql('ALTER TABLE trip ADD CONSTRAINT FK_7656F53B9395C3F3 FOREIGN KEY (customer_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE trip ADD CONSTRAINT FK_7656F53BD5A4816F FOREIGN KEY (bike_id) REFERENCES bike (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE trip_id_seq CASCADE');
        $this->addSql('ALTER TABLE trip DROP CONSTRAINT FK_7656F53B9395C3F3');
        $this->addSql('ALTER TABLE trip DROP CONSTRAINT FK_7656F53BD5A4816F');
        $this->addSql('DROP TABLE trip');
    }
}
