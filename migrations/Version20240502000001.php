<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240502000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create reponse table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE IF NOT EXISTS reponse (
            id INT AUTO_INCREMENT PRIMARY KEY,
            Id_Reclamation INT DEFAULT NULL,
            Date_reponse DATE DEFAULT NULL,
            Contenu TEXT DEFAULT NULL,
            status VARCHAR(255) NOT NULL,
            INDEX IDX_5FB6DEC79D8A5EFC (Id_Reclamation),
            CONSTRAINT FK_REPONSE_RECLAMATION FOREIGN KEY (Id_Reclamation) REFERENCES reclamation (IdReclamation) ON DELETE CASCADE
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE reponse');
    }
} 