<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220902195942 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add  ignore fields to dekor-db';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE mavu_global_dekor ADD ignore_defaults TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE mavu_global_dekor DROP ignore_defaults');
    }
}
