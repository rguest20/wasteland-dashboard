<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260223153850 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE npc_skill DROP FOREIGN KEY `FK_161203820185A18`');
        $this->addSql('ALTER TABLE npc_skill DROP FOREIGN KEY `FK_16120385A6C0D6B`');
        $this->addSql('DROP INDEX IDX_161203820185A18 ON npc_skill');
        $this->addSql('DROP INDEX IDX_16120385A6C0D6B ON npc_skill');
        $this->addSql('ALTER TABLE npc_skill ADD level INT NOT NULL, ADD npc_id_id INT NOT NULL, ADD skill_id_id INT NOT NULL, DROP npc_id, DROP skill_id');
        $this->addSql('ALTER TABLE npc_skill ADD CONSTRAINT FK_161203820185A18 FOREIGN KEY (npc_id_id) REFERENCES npc (id)');
        $this->addSql('ALTER TABLE npc_skill ADD CONSTRAINT FK_16120385A6C0D6B FOREIGN KEY (skill_id_id) REFERENCES skill (id)');
        $this->addSql('CREATE INDEX IDX_161203820185A18 ON npc_skill (npc_id_id)');
        $this->addSql('CREATE INDEX IDX_16120385A6C0D6B ON npc_skill (skill_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE npc_skill DROP FOREIGN KEY FK_161203820185A18');
        $this->addSql('ALTER TABLE npc_skill DROP FOREIGN KEY FK_16120385A6C0D6B');
        $this->addSql('DROP INDEX IDX_161203820185A18 ON npc_skill');
        $this->addSql('DROP INDEX IDX_16120385A6C0D6B ON npc_skill');
        $this->addSql('ALTER TABLE npc_skill ADD npc_id INT NOT NULL, ADD skill_id INT NOT NULL, DROP level, DROP npc_id_id, DROP skill_id_id');
        $this->addSql('ALTER TABLE npc_skill ADD CONSTRAINT `FK_161203820185A18` FOREIGN KEY (npc_id) REFERENCES npc (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE npc_skill ADD CONSTRAINT `FK_16120385A6C0D6B` FOREIGN KEY (skill_id) REFERENCES skill (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_161203820185A18 ON npc_skill (npc_id)');
        $this->addSql('CREATE INDEX IDX_16120385A6C0D6B ON npc_skill (skill_id)');
    }
}
