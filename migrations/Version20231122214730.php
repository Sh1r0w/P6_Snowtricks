<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231122214730 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C275ED078 FOREIGN KEY (profil_id) REFERENCES profil (id)');
        $this->addSql('ALTER TABLE figure ADD profil_id INT NOT NULL, ADD datetime_add DATETIME NOT NULL, CHANGE category categories_id INT NOT NULL');
        $this->addSql('ALTER TABLE figure ADD CONSTRAINT FK_2F57B37AA21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE figure ADD CONSTRAINT FK_2F57B37A275ED078 FOREIGN KEY (profil_id) REFERENCES profil (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2F57B37AA21214B7 ON figure (categories_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2F57B37A275ED078 ON figure (profil_id)');
        $this->addSql('ALTER TABLE profil ADD CONSTRAINT FK_E6D6B2971A14240B FOREIGN KEY (id_connect_id) REFERENCES connect (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C275ED078');
        $this->addSql('ALTER TABLE figure DROP FOREIGN KEY FK_2F57B37AA21214B7');
        $this->addSql('ALTER TABLE figure DROP FOREIGN KEY FK_2F57B37A275ED078');
        $this->addSql('DROP INDEX UNIQ_2F57B37AA21214B7 ON figure');
        $this->addSql('DROP INDEX UNIQ_2F57B37A275ED078 ON figure');
        $this->addSql('ALTER TABLE figure ADD category INT NOT NULL, DROP categories_id, DROP profil_id, DROP datetime_add');
        $this->addSql('ALTER TABLE profil DROP FOREIGN KEY FK_E6D6B2971A14240B');
    }
}
