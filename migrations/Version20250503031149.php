<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250503031149 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mensaje ADD denuncia_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE mensaje ADD CONSTRAINT FK_9B631D0117048D94 FOREIGN KEY (denuncia_id) REFERENCES denuncia (id)');
        $this->addSql('CREATE INDEX IDX_9B631D0117048D94 ON mensaje (denuncia_id)');
        $this->addSql('ALTER TABLE reporte DROP FOREIGN KEY FK_5CB121414D45BBE');
        $this->addSql('DROP INDEX IDX_5CB121414D45BBE ON reporte');
        $this->addSql('ALTER TABLE reporte CHANGE autor_id usuario_id INT NOT NULL, CHANGE fecha_generacion fecha_hora DATETIME NOT NULL');
        $this->addSql('ALTER TABLE reporte ADD CONSTRAINT FK_5CB1214DB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('CREATE INDEX IDX_5CB1214DB38439E ON reporte (usuario_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reporte DROP FOREIGN KEY FK_5CB1214DB38439E');
        $this->addSql('DROP INDEX IDX_5CB1214DB38439E ON reporte');
        $this->addSql('ALTER TABLE reporte CHANGE usuario_id autor_id INT NOT NULL, CHANGE fecha_hora fecha_generacion DATETIME NOT NULL');
        $this->addSql('ALTER TABLE reporte ADD CONSTRAINT FK_5CB121414D45BBE FOREIGN KEY (autor_id) REFERENCES usuario (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_5CB121414D45BBE ON reporte (autor_id)');
        $this->addSql('ALTER TABLE mensaje DROP FOREIGN KEY FK_9B631D0117048D94');
        $this->addSql('DROP INDEX IDX_9B631D0117048D94 ON mensaje');
        $this->addSql('ALTER TABLE mensaje DROP denuncia_id');
    }
}
