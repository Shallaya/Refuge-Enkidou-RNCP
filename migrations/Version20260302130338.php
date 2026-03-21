<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260302130338 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_variant ADD weight_unit VARCHAR(5) DEFAULT NULL, ADD volume_value NUMERIC(5, 2) DEFAULT NULL, ADD volume_unit VARCHAR(5) DEFAULT NULL, CHANGE sku sku VARCHAR(150) NOT NULL, CHANGE color color VARCHAR(50) DEFAULT NULL, CHANGE size size VARCHAR(20) DEFAULT NULL, CHANGE weight weight_value NUMERIC(5, 2) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_variant ADD weight NUMERIC(5, 2) DEFAULT NULL, DROP weight_value, DROP weight_unit, DROP volume_value, DROP volume_unit, CHANGE sku sku VARCHAR(100) NOT NULL, CHANGE color color VARCHAR(100) DEFAULT NULL, CHANGE size size VARCHAR(100) DEFAULT NULL');
    }
}
