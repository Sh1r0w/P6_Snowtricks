<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231107224303 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE st_profil DROP INDEX UNIQ_85B4FEBE361E033F, ADD INDEX IDX_85B4FEBE361E033F (st_connect)');
        $this->addSql('ALTER TABLE st_profil CHANGE st_connect st_connect INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE st_profil DROP INDEX IDX_85B4FEBE361E033F, ADD UNIQUE INDEX UNIQ_85B4FEBE361E033F (st_connect)');
        $this->addSql('ALTER TABLE st_profil CHANGE st_connect st_connect INT DEFAULT NULL');
    }
}
