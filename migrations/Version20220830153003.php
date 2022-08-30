<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220830153003 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE carousel_picture (carousel_id INT NOT NULL, picture_id INT NOT NULL, INDEX IDX_74749AA7C1CE5B98 (carousel_id), INDEX IDX_74749AA7EE45BDBF (picture_id), PRIMARY KEY(carousel_id, picture_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE carousel_picture ADD CONSTRAINT FK_74749AA7C1CE5B98 FOREIGN KEY (carousel_id) REFERENCES carousel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE carousel_picture ADD CONSTRAINT FK_74749AA7EE45BDBF FOREIGN KEY (picture_id) REFERENCES picture (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE carousel DROP img');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE carousel_picture DROP FOREIGN KEY FK_74749AA7C1CE5B98');
        $this->addSql('ALTER TABLE carousel_picture DROP FOREIGN KEY FK_74749AA7EE45BDBF');
        $this->addSql('DROP TABLE carousel_picture');
        $this->addSql('ALTER TABLE carousel ADD img VARCHAR(255) NOT NULL');
    }
}
