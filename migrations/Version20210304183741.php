<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210304183741 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE booking (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, event_id INT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_E00CEDDE19EB6921 (client_id), INDEX IDX_E00CEDDE71F7E88B (event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, surname VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, cookie VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, start_time TIME NOT NULL, end_time TIME NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE infection (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, diagnosed_on DATE NOT NULL, INDEX IDX_5651872019EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE71F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE infection ADD CONSTRAINT FK_5651872019EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDE19EB6921');
        $this->addSql('ALTER TABLE infection DROP FOREIGN KEY FK_5651872019EB6921');
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDE71F7E88B');
        $this->addSql('DROP TABLE booking');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE infection');
    }
}
