<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250212175242 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE denuncia ADD ubicacion_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE denuncia ADD CONSTRAINT FK_F423679657E759E8 FOREIGN KEY (ubicacion_id) REFERENCES ubicacion (id)');
        $this->addSql('CREATE INDEX IDX_F423679657E759E8 ON denuncia (ubicacion_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE denuncia DROP FOREIGN KEY FK_F423679657E759E8');
        $this->addSql('DROP INDEX IDX_F423679657E759E8 ON denuncia');
        $this->addSql('ALTER TABLE denuncia DROP ubicacion_id');
    }
}
