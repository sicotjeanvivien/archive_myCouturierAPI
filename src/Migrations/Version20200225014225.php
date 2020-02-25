<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200225014225 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE prestations (id INT AUTO_INCREMENT NOT NULL, retouching_id INT DEFAULT NULL, client_id INT DEFAULT NULL, history_id INT DEFAULT NULL, INDEX IDX_B338D8D1613B8DBF (retouching_id), INDEX IDX_B338D8D119EB6921 (client_id), INDEX IDX_B338D8D11E058452 (history_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE prestations ADD CONSTRAINT FK_B338D8D1613B8DBF FOREIGN KEY (retouching_id) REFERENCES user_price_retouching (id)');
        $this->addSql('ALTER TABLE prestations ADD CONSTRAINT FK_B338D8D119EB6921 FOREIGN KEY (client_id) REFERENCES user_app (id)');
        $this->addSql('ALTER TABLE prestations ADD CONSTRAINT FK_B338D8D11E058452 FOREIGN KEY (history_id) REFERENCES prestation_history (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE prestations');
    }
}
