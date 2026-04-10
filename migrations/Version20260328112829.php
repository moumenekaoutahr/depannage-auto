<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260328112829 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE form_answers (id INT AUTO_INCREMENT NOT NULL, valeur LONGTEXT DEFAULT NULL, submission_id INT NOT NULL, field_id INT NOT NULL, INDEX IDX_61BCA63DE1FD4933 (submission_id), INDEX IDX_61BCA63D443707B0 (field_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE form_fields (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(50) NOT NULL, label VARCHAR(255) NOT NULL, placeholder VARCHAR(255) DEFAULT NULL, required TINYINT DEFAULT 0 NOT NULL, ordre INT NOT NULL, options JSON DEFAULT NULL, form_id INT NOT NULL, INDEX IDX_7C0B37265FF69B7D (form_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE form_submissions (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, form_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_C80AF9E65FF69B7D (form_id), INDEX IDX_C80AF9E6A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE forms (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, published TINYINT DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, created_by_id INT NOT NULL, INDEX IDX_FD3F1BF7B03A8386 (created_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE form_answers ADD CONSTRAINT FK_61BCA63DE1FD4933 FOREIGN KEY (submission_id) REFERENCES form_submissions (id)');
        $this->addSql('ALTER TABLE form_answers ADD CONSTRAINT FK_61BCA63D443707B0 FOREIGN KEY (field_id) REFERENCES form_fields (id)');
        $this->addSql('ALTER TABLE form_fields ADD CONSTRAINT FK_7C0B37265FF69B7D FOREIGN KEY (form_id) REFERENCES forms (id)');
        $this->addSql('ALTER TABLE form_submissions ADD CONSTRAINT FK_C80AF9E65FF69B7D FOREIGN KEY (form_id) REFERENCES forms (id)');
        $this->addSql('ALTER TABLE form_submissions ADD CONSTRAINT FK_C80AF9E6A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE forms ADD CONSTRAINT FK_FD3F1BF7B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE form_answers DROP FOREIGN KEY FK_61BCA63DE1FD4933');
        $this->addSql('ALTER TABLE form_answers DROP FOREIGN KEY FK_61BCA63D443707B0');
        $this->addSql('ALTER TABLE form_fields DROP FOREIGN KEY FK_7C0B37265FF69B7D');
        $this->addSql('ALTER TABLE form_submissions DROP FOREIGN KEY FK_C80AF9E65FF69B7D');
        $this->addSql('ALTER TABLE form_submissions DROP FOREIGN KEY FK_C80AF9E6A76ED395');
        $this->addSql('ALTER TABLE forms DROP FOREIGN KEY FK_FD3F1BF7B03A8386');
        $this->addSql('DROP TABLE form_answers');
        $this->addSql('DROP TABLE form_fields');
        $this->addSql('DROP TABLE form_submissions');
        $this->addSql('DROP TABLE forms');
    }
}
