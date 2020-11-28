<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201128055009 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE news (id INT AUTO_INCREMENT NOT NULL, translated_document_id INT DEFAULT NULL, reference_name VARCHAR(180) NOT NULL, is_english_global_translation TINYINT(1) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, enabled TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_1DD39950984C6D6A (translated_document_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE news_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, title VARCHAR(180) NOT NULL, description LONGTEXT DEFAULT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_9D5CF3202C2AC5D3 (translatable_id), UNIQUE INDEX news_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE news ADD CONSTRAINT FK_1DD39950984C6D6A FOREIGN KEY (translated_document_id) REFERENCES translated_document (id)');
        $this->addSql('ALTER TABLE news_translation ADD CONSTRAINT FK_9D5CF3202C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES news (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE news_translation DROP FOREIGN KEY FK_9D5CF3202C2AC5D3');
        $this->addSql('DROP TABLE news');
        $this->addSql('DROP TABLE news_translation');
    }
}
