<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210312130744 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDE71F7E88B');
        $this->addSql('CREATE TABLE client_booking (client_id INT NOT NULL, booking_id INT NOT NULL, INDEX IDX_B231A9A319EB6921 (client_id), INDEX IDX_B231A9A33301C60 (booking_id), PRIMARY KEY(client_id, booking_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE client_booking ADD CONSTRAINT FK_B231A9A319EB6921 FOREIGN KEY (client_id) REFERENCES client (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE client_booking ADD CONSTRAINT FK_B231A9A33301C60 FOREIGN KEY (booking_id) REFERENCES booking (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE event');
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDE19EB6921');
        $this->addSql('DROP INDEX IDX_E00CEDDE71F7E88B ON booking');
        $this->addSql('DROP INDEX IDX_E00CEDDE19EB6921 ON booking');
        $this->addSql('ALTER TABLE booking ADD name VARCHAR(255) NOT NULL, ADD date DATE NOT NULL, ADD start_time TIME NOT NULL, ADD end_time TIME NOT NULL, ADD max_clients INT DEFAULT NULL, DROP client_id, DROP event_id, DROP created_at');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, start_time TIME NOT NULL, end_time TIME NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE client_booking');
        $this->addSql('ALTER TABLE booking ADD client_id INT NOT NULL, ADD event_id INT NOT NULL, ADD created_at DATETIME NOT NULL, DROP name, DROP date, DROP start_time, DROP end_time, DROP max_clients');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE71F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('CREATE INDEX IDX_E00CEDDE71F7E88B ON booking (event_id)');
        $this->addSql('CREATE INDEX IDX_E00CEDDE19EB6921 ON booking (client_id)');
    }
}
