<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250219142447 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reporte ADD denuncia_id INT NOT NULL, CHANGE titulo titulo VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE reporte ADD CONSTRAINT FK_5CB121417048D94 FOREIGN KEY (denuncia_id) REFERENCES denuncia (id)');
        $this->addSql('CREATE INDEX IDX_5CB121417048D94 ON reporte (denuncia_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reporte DROP FOREIGN KEY FK_5CB121417048D94');
        $this->addSql('DROP INDEX IDX_5CB121417048D94 ON reporte');
        $this->addSql('ALTER TABLE reporte DROP denuncia_id, CHANGE titulo titulo VARCHAR(255) NOT NULL');
    }
}
