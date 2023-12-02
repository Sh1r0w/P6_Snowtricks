<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231202234314 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE figure ADD CONSTRAINT FK_2F57B37AA21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE figure ADD CONSTRAINT FK_2F57B37A275ED078 FOREIGN KEY (profil_id) REFERENCES profil (id)');
        $this->addSql('CREATE INDEX IDX_2F57B37AA21214B7 ON figure (categories_id)');
        $this->addSql('CREATE INDEX IDX_2F57B37A275ED078 ON figure (profil_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE figure DROP FOREIGN KEY FK_2F57B37AA21214B7');
        $this->addSql('ALTER TABLE figure DROP FOREIGN KEY FK_2F57B37A275ED078');
        $this->addSql('DROP INDEX IDX_2F57B37AA21214B7 ON figure');
        $this->addSql('DROP INDEX IDX_2F57B37A275ED078 ON figure');
    }
}
