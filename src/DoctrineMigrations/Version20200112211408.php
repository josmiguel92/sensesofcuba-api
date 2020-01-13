<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200112211408 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE user_role');
        $this->addSql('ALTER TABLE role ADD id INT AUTO_INCREMENT NOT NULL COMMENT \'(DC2Type:msgphp_user_id)\', DROP name, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE user ADD roles_id INT DEFAULT NULL COMMENT \'(DC2Type:msgphp_user_id)\'');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64938C751C4 FOREIGN KEY (roles_id) REFERENCES role (id)');
        $this->addSql('CREATE INDEX IDX_8D93D64938C751C4 ON user (roles_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_role (user_id INT NOT NULL COMMENT \'(DC2Type:msgphp_user_id)\', role_name VARCHAR(191) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_2DE8C6A3A76ED395 (user_id), INDEX IDX_2DE8C6A3E09C0C92 (role_name), PRIMARY KEY(user_id, role_name)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE user_role ADD CONSTRAINT FK_2DE8C6A3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_role ADD CONSTRAINT FK_2DE8C6A3E09C0C92 FOREIGN KEY (role_name) REFERENCES role (name) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE role MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE role DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE role ADD name VARCHAR(191) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP id');
        $this->addSql('ALTER TABLE role ADD PRIMARY KEY (name)');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64938C751C4');
        $this->addSql('DROP INDEX IDX_8D93D64938C751C4 ON user');
        $this->addSql('ALTER TABLE user DROP roles_id');
    }
}
