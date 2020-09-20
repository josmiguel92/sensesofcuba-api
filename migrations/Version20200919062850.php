<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200919062850 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product_notification (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, is_completed TINYINT(1) NOT NULL, description VARCHAR(180) DEFAULT NULL, target_users LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, enabled TINYINT(1) NOT NULL, INDEX IDX_32F054A14584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product_notification ADD CONSTRAINT FK_32F054A14584665A FOREIGN KEY (product_id) REFERENCES soc_product (id)');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A763DA5256D');
        $this->addSql('DROP INDEX IDX_D8698A763DA5256D ON document');
        $this->addSql('ALTER TABLE document DROP image_id');
        $this->addSql('ALTER TABLE soc_product ADD alternative_translated_document_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE soc_product ADD CONSTRAINT FK_8B655D3BE7B1618 FOREIGN KEY (alternative_translated_document_id) REFERENCES translated_document (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8B655D3BE7B1618 ON soc_product (alternative_translated_document_id)');
        $this->addSql('ALTER TABLE user ADD prefered_lang VARCHAR(2) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE product_notification');
        $this->addSql('ALTER TABLE document ADD image_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A763DA5256D FOREIGN KEY (image_id) REFERENCES soc_image (id)');
        $this->addSql('CREATE INDEX IDX_D8698A763DA5256D ON document (image_id)');
        $this->addSql('ALTER TABLE soc_product DROP FOREIGN KEY FK_8B655D3BE7B1618');
        $this->addSql('DROP INDEX UNIQ_8B655D3BE7B1618 ON soc_product');
        $this->addSql('ALTER TABLE soc_product DROP alternative_translated_document_id');
        $this->addSql('ALTER TABLE user DROP prefered_lang');
    }
}
