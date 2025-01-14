<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250114215542 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE kaina ADD paslauga_id INT NOT NULL');
        $this->addSql('ALTER TABLE kaina ADD CONSTRAINT FK_2D97C66FA24C3A8F FOREIGN KEY (paslauga_id) REFERENCES paslauga (id)');
        $this->addSql('CREATE INDEX IDX_2D97C66FA24C3A8F ON kaina (paslauga_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE kaina DROP FOREIGN KEY FK_2D97C66FA24C3A8F');
        $this->addSql('DROP INDEX IDX_2D97C66FA24C3A8F ON kaina');
        $this->addSql('ALTER TABLE kaina DROP paslauga_id');
    }
}
