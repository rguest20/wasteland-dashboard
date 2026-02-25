<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260224112739 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE knowledge ADD npc_id INT NOT NULL');
        $this->addSql('ALTER TABLE knowledge ADD CONSTRAINT FK_9E072E1DCA7D6B89 FOREIGN KEY (npc_id) REFERENCES npc (id)');
        $this->addSql('CREATE INDEX IDX_9E072E1DCA7D6B89 ON knowledge (npc_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE knowledge DROP FOREIGN KEY FK_9E072E1DCA7D6B89');
        $this->addSql('DROP INDEX IDX_9E072E1DCA7D6B89 ON knowledge');
        $this->addSql('ALTER TABLE knowledge DROP npc_id');
    }
}
