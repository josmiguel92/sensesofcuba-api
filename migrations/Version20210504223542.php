<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210504223542 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE document CHANGE is_english_global_translation is_english_global_translation TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE news CHANGE is_english_global_translation is_english_global_translation TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE soc_product CHANGE is_english_global_translation is_english_global_translation TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE translated_document ADD is_english_global_translation TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE document CHANGE is_english_global_translation is_english_global_translation TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE news CHANGE is_english_global_translation is_english_global_translation TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE soc_product CHANGE is_english_global_translation is_english_global_translation TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE translated_document DROP is_english_global_translation');
    }
}
