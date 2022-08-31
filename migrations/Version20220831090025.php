<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220831090025 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE occasion_picture (occasion_id INT NOT NULL, picture_id INT NOT NULL, INDEX IDX_E03BA8B44034998F (occasion_id), INDEX IDX_E03BA8B4EE45BDBF (picture_id), PRIMARY KEY(occasion_id, picture_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE occasion_picture ADD CONSTRAINT FK_E03BA8B44034998F FOREIGN KEY (occasion_id) REFERENCES occasion (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE occasion_picture ADD CONSTRAINT FK_E03BA8B4EE45BDBF FOREIGN KEY (picture_id) REFERENCES picture (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE occasion_picture DROP FOREIGN KEY FK_E03BA8B44034998F');
        $this->addSql('ALTER TABLE occasion_picture DROP FOREIGN KEY FK_E03BA8B4EE45BDBF');
        $this->addSql('DROP TABLE occasion_picture');
    }
}
