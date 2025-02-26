<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250224125619 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE usuario DROP fecha_nacimiento, DROP genero, DROP avatar, DROP token');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE usuario ADD fecha_nacimiento DATE DEFAULT NULL, ADD genero VARCHAR(255) DEFAULT NULL, ADD avatar VARCHAR(255) DEFAULT NULL, ADD token VARCHAR(255) DEFAULT NULL');
    }
}
