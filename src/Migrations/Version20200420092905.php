<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200420092905 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE prestations DROP FOREIGN KEY FK_B338D8D1613B8DBF');
        $this->addSql('DROP INDEX IDX_B338D8D1613B8DBF ON prestations');
        $this->addSql('ALTER TABLE prestations DROP retouching_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE prestations ADD retouching_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE prestations ADD CONSTRAINT FK_B338D8D1613B8DBF FOREIGN KEY (retouching_id) REFERENCES user_price_retouching (id)');
        $this->addSql('CREATE INDEX IDX_B338D8D1613B8DBF ON prestations (retouching_id)');
    }
}
