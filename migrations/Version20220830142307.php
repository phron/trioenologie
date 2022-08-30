<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220830142307 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE article_picture (article_id INT NOT NULL, picture_id INT NOT NULL, INDEX IDX_FB090B3E7294869C (article_id), INDEX IDX_FB090B3EEE45BDBF (picture_id), PRIMARY KEY(article_id, picture_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE article_picture ADD CONSTRAINT FK_FB090B3E7294869C FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE article_picture ADD CONSTRAINT FK_FB090B3EEE45BDBF FOREIGN KEY (picture_id) REFERENCES picture (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article_picture DROP FOREIGN KEY FK_FB090B3E7294869C');
        $this->addSql('ALTER TABLE article_picture DROP FOREIGN KEY FK_FB090B3EEE45BDBF');
        $this->addSql('DROP TABLE article_picture');
    }
}
