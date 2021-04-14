<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210414041232 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE user CHANGE id id int(11) NOT NULL AUTO_INCREMENT FIRST');
        $this->addSql('ALTER TABLE user_hidden_products CHANGE user_id user_id int(11) NOT NULL FIRST');
        $this->addSql('ALTER TABLE user_soc_product CHANGE user_id user_id int(11) NOT NULL FIRST;');

        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE document_translation CHANGE locale locale VARCHAR(5) NOT NULL');
        $this->addSql('ALTER TABLE news CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE news_translation CHANGE locale locale VARCHAR(5) NOT NULL');
        $this->addSql('ALTER TABLE soc_product_translation CHANGE locale locale VARCHAR(5) NOT NULL');
        $this->addSql('DROP INDEX UNIQ_8D93D649A5D24B55 ON user');
        $this->addSql('DROP INDEX UNIQ_8D93D6496B7BA4B6 ON user');
        $this->addSql('DROP INDEX UNIQ_8D93D649C05FB297 ON user');
        $this->addSql('ALTER TABLE user ADD roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', ADD updated_at DATETIME NOT NULL, DROP role, DROP credential_password, CHANGE name name VARCHAR(191) NOT NULL, CHANGE enterprise enterprise VARCHAR(191) NOT NULL, CHANGE travel_agency travel_agency VARCHAR(191) NOT NULL, CHANGE country country VARCHAR(191) NOT NULL, CHANGE web web VARCHAR(191) DEFAULT NULL, CHANGE email email VARCHAR(191) NOT NULL, CHANGE confirmation_token confirmation_token VARCHAR(180) DEFAULT NULL, CHANGE credential_email password VARCHAR(191) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE document_translation CHANGE locale locale VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE news CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE news_translation CHANGE locale locale VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE soc_product_translation CHANGE locale locale VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON user');
        $this->addSql('ALTER TABLE user ADD role VARCHAR(180) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD credential_password VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP roles, DROP updated_at, CHANGE name name VARCHAR(180) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE enterprise enterprise VARCHAR(180) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE travel_agency travel_agency VARCHAR(180) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE country country VARCHAR(180) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE web web VARCHAR(180) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE email email VARCHAR(180) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE confirmation_token confirmation_token VARCHAR(191) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE password credential_email VARCHAR(191) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649A5D24B55 ON user (credential_email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6496B7BA4B6 ON user (password_reset_token)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649C05FB297 ON user (confirmation_token)');
    }
}
