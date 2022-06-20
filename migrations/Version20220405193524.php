<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220405193524 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add fields to dekor-db';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE mavu_mysite_dekor ADD slug VARCHAR(255) DEFAULT NULL, ADD notes LONGTEXT DEFAULT NULL');
        
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE mavu_mysite_dekor DROP slug, DROP notes');
    }
}
