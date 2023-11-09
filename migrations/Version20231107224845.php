<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231107224845 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE st_profil DROP FOREIGN KEY FK_85B4FEBE361E033F');
        $this->addSql('ALTER TABLE st_profil ADD CONSTRAINT FK_85B4FEBE361E033F FOREIGN KEY (st_connect) REFERENCES st_connect (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE st_profil DROP FOREIGN KEY FK_85B4FEBE361E033F');
        $this->addSql('ALTER TABLE st_profil ADD CONSTRAINT FK_85B4FEBE361E033F FOREIGN KEY (st_connect) REFERENCES st_connect (id)');
    }
}
