<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210402203415 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE translated_document ADD soc_product_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE translated_document ADD CONSTRAINT FK_C69D2F587CB27904 FOREIGN KEY (soc_product_id) REFERENCES soc_product (id)');
        $this->addSql('CREATE INDEX IDX_C69D2F587CB27904 ON translated_document (soc_product_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE translated_document DROP FOREIGN KEY FK_C69D2F587CB27904');
        $this->addSql('DROP INDEX IDX_C69D2F587CB27904 ON translated_document');
        $this->addSql('ALTER TABLE translated_document DROP soc_product_id');
    }
}
