<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231107223640 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE st_profil ADD st_connect INT DEFAULT NULL');
        $this->addSql('ALTER TABLE st_profil ADD CONSTRAINT FK_85B4FEBE361E033F FOREIGN KEY (st_connect) REFERENCES st_connect (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_85B4FEBE361E033F ON st_profil (st_connect)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE st_profil DROP FOREIGN KEY FK_85B4FEBE361E033F');
        $this->addSql('DROP INDEX UNIQ_85B4FEBE361E033F ON st_profil');
        $this->addSql('ALTER TABLE st_profil DROP st_connect');
    }
}
