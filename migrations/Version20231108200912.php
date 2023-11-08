<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231108200912 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE st_comment (id INT AUTO_INCREMENT NOT NULL, st_profil INT NOT NULL, st_figure INT NOT NULL, comment VARCHAR(255) NOT NULL, date DATETIME NOT NULL, UNIQUE INDEX UNIQ_D6A5A84C85B4FEBE (st_profil), UNIQUE INDEX UNIQ_D6A5A84C4C35FF53 (st_figure), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE st_figure (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, `group` INT NOT NULL, media VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE st_comment ADD CONSTRAINT FK_D6A5A84C85B4FEBE FOREIGN KEY (st_profil) REFERENCES st_profil (id)');
        $this->addSql('ALTER TABLE st_comment ADD CONSTRAINT FK_D6A5A84C4C35FF53 FOREIGN KEY (st_figure) REFERENCES st_figure (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE st_comment DROP FOREIGN KEY FK_D6A5A84C85B4FEBE');
        $this->addSql('ALTER TABLE st_comment DROP FOREIGN KEY FK_D6A5A84C4C35FF53');
        $this->addSql('DROP TABLE st_comment');
        $this->addSql('DROP TABLE st_figure');
    }
}
