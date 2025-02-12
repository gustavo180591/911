<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250212154422 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rol_permiso DROP FOREIGN KEY FK_BB62E2196CEFAD37');
        $this->addSql('ALTER TABLE rol_permiso DROP FOREIGN KEY FK_BB62E2194BAB96C');
        $this->addSql('DROP TABLE rol_permiso');
        $this->addSql('ALTER TABLE denuncia DROP FOREIGN KEY FK_F4236796DB38439E');
        $this->addSql('ALTER TABLE denuncia DROP FOREIGN KEY FK_F42367969F5A440B');
        $this->addSql('ALTER TABLE denuncia DROP FOREIGN KEY FK_F423679657E759E8');
        $this->addSql('DROP INDEX IDX_F42367969F5A440B ON denuncia');
        $this->addSql('DROP INDEX IDX_F423679657E759E8 ON denuncia');
        $this->addSql('DROP INDEX IDX_F4236796DB38439E ON denuncia');
        $this->addSql('ALTER TABLE denuncia ADD direccion VARCHAR(255) DEFAULT NULL, DROP estado_id, DROP ubicacion_id, DROP usuario_id, DROP fecha_creacion, DROP fecha_actualizacion, CHANGE fecha_hora fecha_hora DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE usuario DROP FOREIGN KEY FK_2265B05D4BAB96C');
        $this->addSql('ALTER TABLE usuario CHANGE rol_id rol_id INT NOT NULL');
        $this->addSql('ALTER TABLE usuario ADD CONSTRAINT FK_2265B05D4BAB96C FOREIGN KEY (rol_id) REFERENCES rol (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rol_permiso (rol_id INT NOT NULL, permiso_id INT NOT NULL, INDEX IDX_BB62E2194BAB96C (rol_id), INDEX IDX_BB62E2196CEFAD37 (permiso_id), PRIMARY KEY(rol_id, permiso_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE rol_permiso ADD CONSTRAINT FK_BB62E2196CEFAD37 FOREIGN KEY (permiso_id) REFERENCES permiso (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE rol_permiso ADD CONSTRAINT FK_BB62E2194BAB96C FOREIGN KEY (rol_id) REFERENCES rol (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE usuario DROP FOREIGN KEY FK_2265B05D4BAB96C');
        $this->addSql('ALTER TABLE usuario CHANGE rol_id rol_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE usuario ADD CONSTRAINT FK_2265B05D4BAB96C FOREIGN KEY (rol_id) REFERENCES rol (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE denuncia ADD estado_id INT NOT NULL, ADD ubicacion_id INT NOT NULL, ADD usuario_id INT NOT NULL, ADD fecha_creacion DATETIME NOT NULL, ADD fecha_actualizacion DATETIME DEFAULT NULL, DROP direccion, CHANGE fecha_hora fecha_hora DATETIME NOT NULL');
        $this->addSql('ALTER TABLE denuncia ADD CONSTRAINT FK_F4236796DB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE denuncia ADD CONSTRAINT FK_F42367969F5A440B FOREIGN KEY (estado_id) REFERENCES estado_denuncia (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE denuncia ADD CONSTRAINT FK_F423679657E759E8 FOREIGN KEY (ubicacion_id) REFERENCES ubicacion (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_F42367969F5A440B ON denuncia (estado_id)');
        $this->addSql('CREATE INDEX IDX_F423679657E759E8 ON denuncia (ubicacion_id)');
        $this->addSql('CREATE INDEX IDX_F4236796DB38439E ON denuncia (usuario_id)');
    }
}
