<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200115005548 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE document (id INT AUTO_INCREMENT NOT NULL, image_id INT DEFAULT NULL, translated_document_id INT DEFAULT NULL, reference_name VARCHAR(180) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, enabled TINYINT(1) NOT NULL, INDEX IDX_D8698A763DA5256D (image_id), UNIQUE INDEX UNIQ_D8698A76984C6D6A (translated_document_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE document_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, name VARCHAR(180) NOT NULL, description LONGTEXT DEFAULT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_36C072052C2AC5D3 (translatable_id), UNIQUE INDEX document_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE soc_file (id INT AUTO_INCREMENT NOT NULL, file_name VARCHAR(180) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE soc_image (id INT AUTO_INCREMENT NOT NULL, image_name VARCHAR(180) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE soc_product (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, file_id INT DEFAULT NULL, image_id INT DEFAULT NULL, translated_document_id INT DEFAULT NULL, reference_name VARCHAR(180) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, enabled TINYINT(1) NOT NULL, INDEX IDX_8B655D3B727ACA70 (parent_id), INDEX IDX_8B655D3B93CB796C (file_id), INDEX IDX_8B655D3B3DA5256D (image_id), UNIQUE INDEX UNIQ_8B655D3B984C6D6A (translated_document_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE soc_product_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, name VARCHAR(180) NOT NULL, description LONGTEXT DEFAULT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_EF8BA41E2C2AC5D3 (translatable_id), UNIQUE INDEX soc_product_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE translated_document (id INT AUTO_INCREMENT NOT NULL, en_file_id INT DEFAULT NULL, es_file_id INT DEFAULT NULL, de_file_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_C69D2F58C244BA0C (en_file_id), UNIQUE INDEX UNIQ_C69D2F589FCB1F2A (es_file_id), UNIQUE INDEX UNIQ_C69D2F58AFD34FEF (de_file_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL COMMENT \'(DC2Type:msgphp_user_id)\', name VARCHAR(180) NOT NULL, enterprise VARCHAR(180) NOT NULL, travel_agency VARCHAR(180) NOT NULL, country VARCHAR(180) NOT NULL, web VARCHAR(180) DEFAULT NULL, email VARCHAR(180) NOT NULL, role VARCHAR(180) NOT NULL, receive_emails TINYINT(1) NOT NULL, was_enabled TINYINT(1) NOT NULL, credential_email VARCHAR(191) NOT NULL, credential_password VARCHAR(255) NOT NULL, password_reset_token VARCHAR(191) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, enabled TINYINT(1) NOT NULL, confirmation_token VARCHAR(191) DEFAULT NULL, confirmed_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649A5D24B55 (credential_email), UNIQUE INDEX UNIQ_8D93D6496B7BA4B6 (password_reset_token), UNIQUE INDEX UNIQ_8D93D649C05FB297 (confirmation_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_soc_product (user_id INT NOT NULL COMMENT \'(DC2Type:msgphp_user_id)\', soc_product_id INT NOT NULL, INDEX IDX_227E94AEA76ED395 (user_id), INDEX IDX_227E94AE7CB27904 (soc_product_id), PRIMARY KEY(user_id, soc_product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_hidden_products (user_id INT NOT NULL COMMENT \'(DC2Type:msgphp_user_id)\', soc_product_id INT NOT NULL, INDEX IDX_1AF374F7A76ED395 (user_id), INDEX IDX_1AF374F77CB27904 (soc_product_id), PRIMARY KEY(user_id, soc_product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A763DA5256D FOREIGN KEY (image_id) REFERENCES soc_image (id)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A76984C6D6A FOREIGN KEY (translated_document_id) REFERENCES translated_document (id)');
        $this->addSql('ALTER TABLE document_translation ADD CONSTRAINT FK_36C072052C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES document (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE soc_product ADD CONSTRAINT FK_8B655D3B727ACA70 FOREIGN KEY (parent_id) REFERENCES soc_product (id)');
        $this->addSql('ALTER TABLE soc_product ADD CONSTRAINT FK_8B655D3B93CB796C FOREIGN KEY (file_id) REFERENCES soc_file (id)');
        $this->addSql('ALTER TABLE soc_product ADD CONSTRAINT FK_8B655D3B3DA5256D FOREIGN KEY (image_id) REFERENCES soc_image (id)');
        $this->addSql('ALTER TABLE soc_product ADD CONSTRAINT FK_8B655D3B984C6D6A FOREIGN KEY (translated_document_id) REFERENCES translated_document (id)');
        $this->addSql('ALTER TABLE soc_product_translation ADD CONSTRAINT FK_EF8BA41E2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES soc_product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE translated_document ADD CONSTRAINT FK_C69D2F58C244BA0C FOREIGN KEY (en_file_id) REFERENCES soc_file (id)');
        $this->addSql('ALTER TABLE translated_document ADD CONSTRAINT FK_C69D2F589FCB1F2A FOREIGN KEY (es_file_id) REFERENCES soc_file (id)');
        $this->addSql('ALTER TABLE translated_document ADD CONSTRAINT FK_C69D2F58AFD34FEF FOREIGN KEY (de_file_id) REFERENCES soc_file (id)');
        $this->addSql('ALTER TABLE user_soc_product ADD CONSTRAINT FK_227E94AEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_soc_product ADD CONSTRAINT FK_227E94AE7CB27904 FOREIGN KEY (soc_product_id) REFERENCES soc_product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_hidden_products ADD CONSTRAINT FK_1AF374F7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_hidden_products ADD CONSTRAINT FK_1AF374F77CB27904 FOREIGN KEY (soc_product_id) REFERENCES soc_product (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE document_translation DROP FOREIGN KEY FK_36C072052C2AC5D3');
        $this->addSql('ALTER TABLE soc_product DROP FOREIGN KEY FK_8B655D3B93CB796C');
        $this->addSql('ALTER TABLE translated_document DROP FOREIGN KEY FK_C69D2F58C244BA0C');
        $this->addSql('ALTER TABLE translated_document DROP FOREIGN KEY FK_C69D2F589FCB1F2A');
        $this->addSql('ALTER TABLE translated_document DROP FOREIGN KEY FK_C69D2F58AFD34FEF');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A763DA5256D');
        $this->addSql('ALTER TABLE soc_product DROP FOREIGN KEY FK_8B655D3B3DA5256D');
        $this->addSql('ALTER TABLE soc_product DROP FOREIGN KEY FK_8B655D3B727ACA70');
        $this->addSql('ALTER TABLE soc_product_translation DROP FOREIGN KEY FK_EF8BA41E2C2AC5D3');
        $this->addSql('ALTER TABLE user_soc_product DROP FOREIGN KEY FK_227E94AE7CB27904');
        $this->addSql('ALTER TABLE user_hidden_products DROP FOREIGN KEY FK_1AF374F77CB27904');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A76984C6D6A');
        $this->addSql('ALTER TABLE soc_product DROP FOREIGN KEY FK_8B655D3B984C6D6A');
        $this->addSql('ALTER TABLE user_soc_product DROP FOREIGN KEY FK_227E94AEA76ED395');
        $this->addSql('ALTER TABLE user_hidden_products DROP FOREIGN KEY FK_1AF374F7A76ED395');
        $this->addSql('DROP TABLE document');
        $this->addSql('DROP TABLE document_translation');
        $this->addSql('DROP TABLE soc_file');
        $this->addSql('DROP TABLE soc_image');
        $this->addSql('DROP TABLE soc_product');
        $this->addSql('DROP TABLE soc_product_translation');
        $this->addSql('DROP TABLE translated_document');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_soc_product');
        $this->addSql('DROP TABLE user_hidden_products');
    }
}
