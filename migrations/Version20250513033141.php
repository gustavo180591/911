<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250513033141 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Agrega el campo estado a la entidad User con valores posibles: pendiente, aprobado, rechazado';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE user ADD estado VARCHAR(20) NOT NULL DEFAULT 'pendiente'
        SQL);

        // Actualizar los usuarios existentes para establecer estado aprobado
        $this->addSql(<<<'SQL'
            UPDATE user SET estado = 'aprobado' WHERE id > 0
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE user DROP estado
        SQL);
    }
}
