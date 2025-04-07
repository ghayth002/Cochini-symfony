<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240502000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create reclamation table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE IF NOT EXISTS reclamation (
            IdReclamation INT AUTO_INCREMENT PRIMARY KEY,
            description TEXT DEFAULT NULL,
            typeR VARCHAR(255) DEFAULT NULL,
            Id_coach INT DEFAULT NULL,
            date DATE NOT NULL,
            Id_adherent INT DEFAULT NULL,
            INDEX IDX_CE60640428AA4EAA (Id_coach),
            INDEX IDX_CE60640446949322 (Id_adherent),
            CONSTRAINT FK_RECLAMATION_COACH FOREIGN KEY (Id_coach) REFERENCES user (id),
            CONSTRAINT FK_RECLAMATION_ADHERENT FOREIGN KEY (Id_adherent) REFERENCES user (id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE reclamation');
    }
} 