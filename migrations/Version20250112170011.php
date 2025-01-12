<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250112170011 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE paslauga (id INT AUTO_INCREMENT NOT NULL, vadybininkas_id INT NOT NULL, vardas VARCHAR(255) NOT NULL, kaina INT NOT NULL, INDEX IDX_F46E7AF816230C22 (vadybininkas_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE paslauga ADD CONSTRAINT FK_F46E7AF816230C22 FOREIGN KEY (vadybininkas_id) REFERENCES naudotojas (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE paslauga DROP FOREIGN KEY FK_F46E7AF816230C22');
        $this->addSql('DROP TABLE paslauga');
    }
}
