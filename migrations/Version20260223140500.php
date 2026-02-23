<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260223140500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add SPECIAL stats columns to npc via embeddable';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE npc ADD strength INT NOT NULL DEFAULT 5, ADD perception INT NOT NULL DEFAULT 5, ADD endurance INT NOT NULL DEFAULT 5, ADD charisma INT NOT NULL DEFAULT 5, ADD intelligence INT NOT NULL DEFAULT 5, ADD agility INT NOT NULL DEFAULT 5, ADD luck INT NOT NULL DEFAULT 5');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE npc DROP strength, DROP perception, DROP endurance, DROP charisma, DROP intelligence, DROP agility, DROP luck');
    }
}
