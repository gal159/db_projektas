<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250112180202 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE naudotojas ADD bendrija_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE naudotojas ADD CONSTRAINT FK_9AEA16139E5D96E FOREIGN KEY (bendrija_id) REFERENCES bendrija (id)');
        $this->addSql('CREATE INDEX IDX_9AEA16139E5D96E ON naudotojas (bendrija_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE naudotojas DROP FOREIGN KEY FK_9AEA16139E5D96E');
        $this->addSql('DROP INDEX IDX_9AEA16139E5D96E ON naudotojas');
        $this->addSql('ALTER TABLE naudotojas DROP bendrija_id');
    }
}
