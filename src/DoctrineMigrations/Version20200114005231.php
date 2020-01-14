<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200114005231 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_soc_product (user_id INT NOT NULL COMMENT \'(DC2Type:msgphp_user_id)\', soc_product_id INT NOT NULL, INDEX IDX_227E94AEA76ED395 (user_id), INDEX IDX_227E94AE7CB27904 (soc_product_id), PRIMARY KEY(user_id, soc_product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_hidden_products (user_id INT NOT NULL COMMENT \'(DC2Type:msgphp_user_id)\', soc_product_id INT NOT NULL, INDEX IDX_1AF374F7A76ED395 (user_id), INDEX IDX_1AF374F77CB27904 (soc_product_id), PRIMARY KEY(user_id, soc_product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_soc_product ADD CONSTRAINT FK_227E94AEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_soc_product ADD CONSTRAINT FK_227E94AE7CB27904 FOREIGN KEY (soc_product_id) REFERENCES soc_product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_hidden_products ADD CONSTRAINT FK_1AF374F7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_hidden_products ADD CONSTRAINT FK_1AF374F77CB27904 FOREIGN KEY (soc_product_id) REFERENCES soc_product (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE role');
        $this->addSql('ALTER TABLE user ADD receive_emails TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(180) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE user_soc_product');
        $this->addSql('DROP TABLE user_hidden_products');
        $this->addSql('ALTER TABLE user DROP receive_emails');
    }
}
