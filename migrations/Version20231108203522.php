<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231108203522 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE st_comment DROP FOREIGN KEY FK_D6A5A84C85B4FEBE');
        $this->addSql('ALTER TABLE st_comment ADD CONSTRAINT FK_D6A5A84C85B4FEBE FOREIGN KEY (st_profil) REFERENCES st_profil (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE st_comment DROP FOREIGN KEY FK_D6A5A84C85B4FEBE');
        $this->addSql('ALTER TABLE st_comment ADD CONSTRAINT FK_D6A5A84C85B4FEBE FOREIGN KEY (st_profil) REFERENCES st_profil (id)');
    }
}
