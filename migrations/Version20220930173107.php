<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220930173107 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add raw css to dekor table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE mavu_global_dekor ADD use_raw_css TINYINT(1) DEFAULT NULL, ADD raw_css LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE mavu_global_dekor DROP use_raw_css, DROP raw_css');
    }
}
