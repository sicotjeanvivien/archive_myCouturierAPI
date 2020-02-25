<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200225010447 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE category_retouching (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE retouching (id INT AUTO_INCREMENT NOT NULL, category_retouching_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_466756207FE0A8F4 (category_retouching_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_price_retouching (id INT AUTO_INCREMENT NOT NULL, user_app_id INT DEFAULT NULL, retouching_id INT DEFAULT NULL, price_couturier INT NOT NULL, price_show_client INT NOT NULL, INDEX IDX_8FFA1BF91CD53A10 (user_app_id), INDEX IDX_8FFA1BF9613B8DBF (retouching_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE retouching ADD CONSTRAINT FK_466756207FE0A8F4 FOREIGN KEY (category_retouching_id) REFERENCES category_retouching (id)');
        $this->addSql('ALTER TABLE user_price_retouching ADD CONSTRAINT FK_8FFA1BF91CD53A10 FOREIGN KEY (user_app_id) REFERENCES user_app (id)');
        $this->addSql('ALTER TABLE user_price_retouching ADD CONSTRAINT FK_8FFA1BF9613B8DBF FOREIGN KEY (retouching_id) REFERENCES retouching (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE retouching DROP FOREIGN KEY FK_466756207FE0A8F4');
        $this->addSql('ALTER TABLE user_price_retouching DROP FOREIGN KEY FK_8FFA1BF9613B8DBF');
        $this->addSql('DROP TABLE category_retouching');
        $this->addSql('DROP TABLE retouching');
        $this->addSql('DROP TABLE user_price_retouching');
    }
}
