<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250213140606 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categoria_denuncia (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, descripcion LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_C54F86863A909126 (nombre), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE denuncia (id INT AUTO_INCREMENT NOT NULL, estado_id INT NOT NULL, categoria_id INT NOT NULL, usuario_id INT NOT NULL, ubicacion_id INT DEFAULT NULL, descripcion LONGTEXT NOT NULL, fecha_hora DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_F42367969F5A440B (estado_id), INDEX IDX_F42367963397707A (categoria_id), INDEX IDX_F4236796DB38439E (usuario_id), INDEX IDX_F423679657E759E8 (ubicacion_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE estadistica (id INT AUTO_INCREMENT NOT NULL, tipo_denuncia VARCHAR(255) NOT NULL, cantidad INT NOT NULL, fecha_generacion DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE estado_denuncia (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(50) NOT NULL, descripcion LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_5D5D47B3A909126 (nombre), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evidencia (id INT AUTO_INCREMENT NOT NULL, denuncia_id INT NOT NULL, ruta_archivo VARCHAR(255) DEFAULT NULL, fecha_subida DATETIME NOT NULL, INDEX IDX_59B9807C17048D94 (denuncia_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE historial_denuncia (id INT AUTO_INCREMENT NOT NULL, denuncia_id INT NOT NULL, estado_anterior_id INT NOT NULL, estado_nuevo_id INT NOT NULL, fecha_cambio DATETIME NOT NULL, INDEX IDX_2C2DFD3117048D94 (denuncia_id), INDEX IDX_2C2DFD312DE312B8 (estado_anterior_id), INDEX IDX_2C2DFD313B0B5170 (estado_nuevo_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE log_sistema (id INT AUTO_INCREMENT NOT NULL, usuario_id INT DEFAULT NULL, accion VARCHAR(255) NOT NULL, fecha_hora DATETIME NOT NULL, detalle LONGTEXT DEFAULT NULL, INDEX IDX_A90F93F6DB38439E (usuario_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mensaje (id INT AUTO_INCREMENT NOT NULL, usuario_remitente_id INT NOT NULL, usuario_destinatario_id INT NOT NULL, contenido LONGTEXT NOT NULL, fecha_envio DATETIME NOT NULL, INDEX IDX_9B631D011606118A (usuario_remitente_id), INDEX IDX_9B631D0150D0D880 (usuario_destinatario_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notificacion (id INT AUTO_INCREMENT NOT NULL, usuario_id INT NOT NULL, denuncia_id INT DEFAULT NULL, mensaje LONGTEXT NOT NULL, fecha_envio DATETIME NOT NULL, INDEX IDX_729A19ECDB38439E (usuario_id), INDEX IDX_729A19EC17048D94 (denuncia_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE permiso (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(50) NOT NULL, descripcion LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_FD7AAB9E3A909126 (nombre), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reporte (id INT AUTO_INCREMENT NOT NULL, autor_id INT NOT NULL, titulo VARCHAR(255) NOT NULL, descripcion LONGTEXT NOT NULL, fecha_generacion DATETIME NOT NULL, INDEX IDX_5CB121414D45BBE (autor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ubicacion (id INT AUTO_INCREMENT NOT NULL, calle VARCHAR(255) NOT NULL, numero VARCHAR(20) DEFAULT NULL, detalles LONGTEXT DEFAULT NULL, coordenadas VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE usuario (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nombre VARCHAR(255) NOT NULL, apellido VARCHAR(255) NOT NULL, dni VARCHAR(20) DEFAULT NULL, telefono VARCHAR(20) DEFAULT NULL, fecha_nacimiento DATE DEFAULT NULL, direccion VARCHAR(255) DEFAULT NULL, genero VARCHAR(255) DEFAULT NULL, avatar VARCHAR(255) DEFAULT NULL, token VARCHAR(255) DEFAULT NULL, fecha_registro DATETIME NOT NULL, UNIQUE INDEX UNIQ_2265B05DE7927C74 (email), UNIQUE INDEX UNIQ_2265B05D7F8F253B (dni), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE denuncia ADD CONSTRAINT FK_F42367969F5A440B FOREIGN KEY (estado_id) REFERENCES estado_denuncia (id)');
        $this->addSql('ALTER TABLE denuncia ADD CONSTRAINT FK_F42367963397707A FOREIGN KEY (categoria_id) REFERENCES categoria_denuncia (id)');
        $this->addSql('ALTER TABLE denuncia ADD CONSTRAINT FK_F4236796DB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE denuncia ADD CONSTRAINT FK_F423679657E759E8 FOREIGN KEY (ubicacion_id) REFERENCES ubicacion (id)');
        $this->addSql('ALTER TABLE evidencia ADD CONSTRAINT FK_59B9807C17048D94 FOREIGN KEY (denuncia_id) REFERENCES denuncia (id)');
        $this->addSql('ALTER TABLE historial_denuncia ADD CONSTRAINT FK_2C2DFD3117048D94 FOREIGN KEY (denuncia_id) REFERENCES denuncia (id)');
        $this->addSql('ALTER TABLE historial_denuncia ADD CONSTRAINT FK_2C2DFD312DE312B8 FOREIGN KEY (estado_anterior_id) REFERENCES estado_denuncia (id)');
        $this->addSql('ALTER TABLE historial_denuncia ADD CONSTRAINT FK_2C2DFD313B0B5170 FOREIGN KEY (estado_nuevo_id) REFERENCES estado_denuncia (id)');
        $this->addSql('ALTER TABLE log_sistema ADD CONSTRAINT FK_A90F93F6DB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE mensaje ADD CONSTRAINT FK_9B631D011606118A FOREIGN KEY (usuario_remitente_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE mensaje ADD CONSTRAINT FK_9B631D0150D0D880 FOREIGN KEY (usuario_destinatario_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE notificacion ADD CONSTRAINT FK_729A19ECDB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE notificacion ADD CONSTRAINT FK_729A19EC17048D94 FOREIGN KEY (denuncia_id) REFERENCES denuncia (id)');
        $this->addSql('ALTER TABLE reporte ADD CONSTRAINT FK_5CB121414D45BBE FOREIGN KEY (autor_id) REFERENCES usuario (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE denuncia DROP FOREIGN KEY FK_F42367969F5A440B');
        $this->addSql('ALTER TABLE denuncia DROP FOREIGN KEY FK_F42367963397707A');
        $this->addSql('ALTER TABLE denuncia DROP FOREIGN KEY FK_F4236796DB38439E');
        $this->addSql('ALTER TABLE denuncia DROP FOREIGN KEY FK_F423679657E759E8');
        $this->addSql('ALTER TABLE evidencia DROP FOREIGN KEY FK_59B9807C17048D94');
        $this->addSql('ALTER TABLE historial_denuncia DROP FOREIGN KEY FK_2C2DFD3117048D94');
        $this->addSql('ALTER TABLE historial_denuncia DROP FOREIGN KEY FK_2C2DFD312DE312B8');
        $this->addSql('ALTER TABLE historial_denuncia DROP FOREIGN KEY FK_2C2DFD313B0B5170');
        $this->addSql('ALTER TABLE log_sistema DROP FOREIGN KEY FK_A90F93F6DB38439E');
        $this->addSql('ALTER TABLE mensaje DROP FOREIGN KEY FK_9B631D011606118A');
        $this->addSql('ALTER TABLE mensaje DROP FOREIGN KEY FK_9B631D0150D0D880');
        $this->addSql('ALTER TABLE notificacion DROP FOREIGN KEY FK_729A19ECDB38439E');
        $this->addSql('ALTER TABLE notificacion DROP FOREIGN KEY FK_729A19EC17048D94');
        $this->addSql('ALTER TABLE reporte DROP FOREIGN KEY FK_5CB121414D45BBE');
        $this->addSql('DROP TABLE categoria_denuncia');
        $this->addSql('DROP TABLE denuncia');
        $this->addSql('DROP TABLE estadistica');
        $this->addSql('DROP TABLE estado_denuncia');
        $this->addSql('DROP TABLE evidencia');
        $this->addSql('DROP TABLE historial_denuncia');
        $this->addSql('DROP TABLE log_sistema');
        $this->addSql('DROP TABLE mensaje');
        $this->addSql('DROP TABLE notificacion');
        $this->addSql('DROP TABLE permiso');
        $this->addSql('DROP TABLE reporte');
        $this->addSql('DROP TABLE ubicacion');
        $this->addSql('DROP TABLE usuario');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
