<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230822124454 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_app_role DROP FOREIGN KEY FK_B2CA4BCAA76ED395');
        $this->addSql('ALTER TABLE user_app_role DROP FOREIGN KEY FK_B2CA4BCA3B5EA2E1');
        $this->addSql('DROP TABLE app_role');
        $this->addSql('DROP TABLE user_app_role');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE app_role (id INT AUTO_INCREMENT NOT NULL, role_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE user_app_role (user_id INT NOT NULL, app_role_id INT NOT NULL, INDEX IDX_B2CA4BCAA76ED395 (user_id), INDEX IDX_B2CA4BCA3B5EA2E1 (app_role_id), PRIMARY KEY(user_id, app_role_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE user_app_role ADD CONSTRAINT FK_B2CA4BCAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_app_role ADD CONSTRAINT FK_B2CA4BCA3B5EA2E1 FOREIGN KEY (app_role_id) REFERENCES app_role (id) ON DELETE CASCADE');
    }
}
