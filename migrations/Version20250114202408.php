<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250114202408 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE kaina (id INT AUTO_INCREMENT NOT NULL, bedrija_id INT NOT NULL, vadybininkas_id INT NOT NULL, kaina INT NOT NULL, INDEX IDX_2D97C66FE9D9AAA9 (bedrija_id), INDEX IDX_2D97C66F16230C22 (vadybininkas_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE kaina ADD CONSTRAINT FK_2D97C66FE9D9AAA9 FOREIGN KEY (bedrija_id) REFERENCES bendrija (id)');
        $this->addSql('ALTER TABLE kaina ADD CONSTRAINT FK_2D97C66F16230C22 FOREIGN KEY (vadybininkas_id) REFERENCES naudotojas (id)');
        $this->addSql('ALTER TABLE paslauga DROP FOREIGN KEY FK_F46E7AF816230C22');
        $this->addSql('DROP INDEX IDX_F46E7AF816230C22 ON paslauga');
        $this->addSql('ALTER TABLE paslauga DROP vadybininkas_id, DROP kaina');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE kaina DROP FOREIGN KEY FK_2D97C66FE9D9AAA9');
        $this->addSql('ALTER TABLE kaina DROP FOREIGN KEY FK_2D97C66F16230C22');
        $this->addSql('DROP TABLE kaina');
        $this->addSql('ALTER TABLE paslauga ADD vadybininkas_id INT NOT NULL, ADD kaina INT NOT NULL');
        $this->addSql('ALTER TABLE paslauga ADD CONSTRAINT FK_F46E7AF816230C22 FOREIGN KEY (vadybininkas_id) REFERENCES naudotojas (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_F46E7AF816230C22 ON paslauga (vadybininkas_id)');
    }
}
