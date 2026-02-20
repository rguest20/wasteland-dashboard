<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260220125000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add location model and npc foreign key to location';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE location (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(120) NOT NULL, defence INT NOT NULL, food INT NOT NULL, morale INT NOT NULL, standing INT NOT NULL, UNIQUE INDEX UNIQ_5E9E89CB5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE npc ADD location_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE npc ADD CONSTRAINT FK_468C762C64D218E FOREIGN KEY (location_id) REFERENCES location (id)');
        $this->addSql('CREATE INDEX IDX_468C762C64D218E ON npc (location_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE npc DROP FOREIGN KEY FK_468C762C64D218E');
        $this->addSql('DROP INDEX IDX_468C762C64D218E ON npc');
        $this->addSql('ALTER TABLE npc DROP location_id');
        $this->addSql('DROP TABLE location');
    }
}
