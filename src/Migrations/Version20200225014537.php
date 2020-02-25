<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200225014537 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE prestation_history ADD prestation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE prestation_history ADD CONSTRAINT FK_E42C83D29E45C554 FOREIGN KEY (prestation_id) REFERENCES prestations (id)');
        $this->addSql('CREATE INDEX IDX_E42C83D29E45C554 ON prestation_history (prestation_id)');
        $this->addSql('ALTER TABLE prestations DROP FOREIGN KEY FK_B338D8D11E058452');
        $this->addSql('DROP INDEX IDX_B338D8D11E058452 ON prestations');
        $this->addSql('ALTER TABLE prestations DROP history_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE prestation_history DROP FOREIGN KEY FK_E42C83D29E45C554');
        $this->addSql('DROP INDEX IDX_E42C83D29E45C554 ON prestation_history');
        $this->addSql('ALTER TABLE prestation_history DROP prestation_id');
        $this->addSql('ALTER TABLE prestations ADD history_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE prestations ADD CONSTRAINT FK_B338D8D11E058452 FOREIGN KEY (history_id) REFERENCES prestation_history (id)');
        $this->addSql('CREATE INDEX IDX_B338D8D11E058452 ON prestations (history_id)');
    }
}
