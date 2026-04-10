<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260401114917 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chat DROP INDEX IDX_659DF2AA8EAE3863, ADD UNIQUE INDEX UNIQ_659DF2AA8EAE3863 (intervention_id)');
        $this->addSql('ALTER TABLE chat ADD fermet_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chat DROP INDEX UNIQ_659DF2AA8EAE3863, ADD INDEX IDX_659DF2AA8EAE3863 (intervention_id)');
        $this->addSql('ALTER TABLE chat DROP fermet_at');
    }
}
