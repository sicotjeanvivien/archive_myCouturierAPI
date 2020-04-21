<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200420092319 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE prestations ADD user_price_retouching_id INT DEFAULT NULL, ADD description LONGTEXT DEFAULT NULL, ADD photo LONGTEXT DEFAULT NULL, ADD accept TINYINT(1) DEFAULT NULL, ADD pay TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE prestations ADD CONSTRAINT FK_B338D8D1280A5D78 FOREIGN KEY (user_price_retouching_id) REFERENCES user_price_retouching (id)');
        $this->addSql('CREATE INDEX IDX_B338D8D1280A5D78 ON prestations (user_price_retouching_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE prestations DROP FOREIGN KEY FK_B338D8D1280A5D78');
        $this->addSql('DROP INDEX IDX_B338D8D1280A5D78 ON prestations');
        $this->addSql('ALTER TABLE prestations DROP user_price_retouching_id, DROP description, DROP photo, DROP accept, DROP pay');
    }
}
