<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230814113854 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project ADD project_lead_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE964B49E8 FOREIGN KEY (project_lead_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_2FB3D0EE964B49E8 ON project (project_lead_id)');
        $this->addSql('ALTER TABLE vacation_request ADD project_lead_approved TINYINT(1) NOT NULL, ADD team_lead_approved TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EE964B49E8');
        $this->addSql('DROP INDEX IDX_2FB3D0EE964B49E8 ON project');
        $this->addSql('ALTER TABLE project DROP project_lead_id');
        $this->addSql('ALTER TABLE vacation_request DROP project_lead_approved, DROP team_lead_approved');
    }
}
