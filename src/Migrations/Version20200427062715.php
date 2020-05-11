<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200427062715 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE commentary (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, couturier_id INT DEFAULT NULL, message LONGTEXT DEFAULT NULL, rating INT DEFAULT NULL, INDEX IDX_1CAC12CAF675F31B (author_id), INDEX IDX_1CAC12CAE3424A00 (couturier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, prestation_id INT DEFAULT NULL, edited_date DATETIME DEFAULT NULL, message LONGTEXT DEFAULT NULL, INDEX IDX_B6BD307FF675F31B (author_id), INDEX IDX_B6BD307F9E45C554 (prestation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commentary ADD CONSTRAINT FK_1CAC12CAF675F31B FOREIGN KEY (author_id) REFERENCES user_app (id)');
        $this->addSql('ALTER TABLE commentary ADD CONSTRAINT FK_1CAC12CAE3424A00 FOREIGN KEY (couturier_id) REFERENCES user_app (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF675F31B FOREIGN KEY (author_id) REFERENCES user_app (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F9E45C554 FOREIGN KEY (prestation_id) REFERENCES prestations (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE commentary');
        $this->addSql('DROP TABLE message');
    }
}
