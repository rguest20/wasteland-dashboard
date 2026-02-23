<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260223152803 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE npc_skill (id INT AUTO_INCREMENT NOT NULL, npc_id INT NOT NULL, skill_id INT NOT NULL, INDEX IDX_161203820185A18 (npc_id), INDEX IDX_16120385A6C0D6B (skill_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE npc_skill ADD CONSTRAINT FK_161203820185A18 FOREIGN KEY (npc_id) REFERENCES npc (id)');
        $this->addSql('ALTER TABLE npc_skill ADD CONSTRAINT FK_16120385A6C0D6B FOREIGN KEY (skill_id) REFERENCES skill (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE npc_skill DROP FOREIGN KEY FK_161203820185A18');
        $this->addSql('ALTER TABLE npc_skill DROP FOREIGN KEY FK_16120385A6C0D6B');
        $this->addSql('DROP TABLE npc_skill');
    }
}
