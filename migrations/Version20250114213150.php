<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250114213150 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bendrija ADD vadybininkas_id INT NOT NULL');
        $this->addSql('ALTER TABLE bendrija ADD CONSTRAINT FK_53B9EE1116230C22 FOREIGN KEY (vadybininkas_id) REFERENCES naudotojas (id)');
        $this->addSql('CREATE INDEX IDX_53B9EE1116230C22 ON bendrija (vadybininkas_id)');
        $this->addSql('ALTER TABLE kaina DROP FOREIGN KEY FK_2D97C66F16230C22');
        $this->addSql('DROP INDEX IDX_2D97C66F16230C22 ON kaina');
        $this->addSql('ALTER TABLE kaina DROP vadybininkas_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bendrija DROP FOREIGN KEY FK_53B9EE1116230C22');
        $this->addSql('DROP INDEX IDX_53B9EE1116230C22 ON bendrija');
        $this->addSql('ALTER TABLE bendrija DROP vadybininkas_id');
        $this->addSql('ALTER TABLE kaina ADD vadybininkas_id INT NOT NULL');
        $this->addSql('ALTER TABLE kaina ADD CONSTRAINT FK_2D97C66F16230C22 FOREIGN KEY (vadybininkas_id) REFERENCES naudotojas (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_2D97C66F16230C22 ON kaina (vadybininkas_id)');
    }
}
