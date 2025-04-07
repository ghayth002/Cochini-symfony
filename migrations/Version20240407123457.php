<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Force add statut field to reclamation table
 */
final class Version20240407123457 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Force add statut field to reclamation table';
    }

    public function up(Schema $schema): void
    {
        // Add the statut column with a try-catch to handle case if already exists
        try {
            $this->addSql('ALTER TABLE reclamation ADD statut TINYINT(1) NOT NULL DEFAULT 0');
        } catch (\Exception $e) {
            // Column may already exist, ignore error
            $this->write('Notice: ' . $e->getMessage());
        }
    }

    public function down(Schema $schema): void
    {
        try {
            $this->addSql('ALTER TABLE reclamation DROP statut');
        } catch (\Exception $e) {
            // May not exist, ignore
            $this->write('Notice: ' . $e->getMessage());
        }
    }
} 