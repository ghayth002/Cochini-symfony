<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Add statut field to reclamation table
 */
final class Version20240407123456 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add statut field to reclamation table';
    }

    public function up(Schema $schema): void
    {
        // If the column already exists, we skip this migration
        $table = $schema->getTable('reclamation');
        if (!$table->hasColumn('statut')) {
            $this->addSql('ALTER TABLE reclamation ADD statut TINYINT(1) NOT NULL DEFAULT 0');
        }
    }

    public function down(Schema $schema): void
    {
        // Only try to drop if the column exists
        $table = $schema->getTable('reclamation');
        if ($table->hasColumn('statut')) {
            $this->addSql('ALTER TABLE reclamation DROP statut');
        }
    }
} 