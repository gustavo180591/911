<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250218032846 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE usuario DROP FOREIGN KEY FK_2265B05D4BAB96C');
        $this->addSql('DROP TABLE rol');
        $this->addSql('ALTER TABLE denuncia ADD estado_id INT NOT NULL, ADD usuario_id INT NOT NULL');
        $this->addSql('ALTER TABLE denuncia ADD CONSTRAINT FK_F42367969F5A440B FOREIGN KEY (estado_id) REFERENCES estado_denuncia (id)');
        $this->addSql('ALTER TABLE denuncia ADD CONSTRAINT FK_F4236796DB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('CREATE INDEX IDX_F42367969F5A440B ON denuncia (estado_id)');
        $this->addSql('CREATE INDEX IDX_F4236796DB38439E ON denuncia (usuario_id)');
        $this->addSql('ALTER TABLE ubicacion DROP detalles');
        $this->addSql('DROP INDEX IDX_2265B05D4BAB96C ON usuario');
        $this->addSql('ALTER TABLE usuario DROP rol_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rol (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, descripcion LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, UNIQUE INDEX UNIQ_E553F373A909126 (nombre), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE usuario ADD rol_id INT NOT NULL');
        $this->addSql('ALTER TABLE usuario ADD CONSTRAINT FK_2265B05D4BAB96C FOREIGN KEY (rol_id) REFERENCES rol (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_2265B05D4BAB96C ON usuario (rol_id)');
        $this->addSql('ALTER TABLE ubicacion ADD detalles LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE denuncia DROP FOREIGN KEY FK_F42367969F5A440B');
        $this->addSql('ALTER TABLE denuncia DROP FOREIGN KEY FK_F4236796DB38439E');
        $this->addSql('DROP INDEX IDX_F42367969F5A440B ON denuncia');
        $this->addSql('DROP INDEX IDX_F4236796DB38439E ON denuncia');
        $this->addSql('ALTER TABLE denuncia DROP estado_id, DROP usuario_id');
    }
}
