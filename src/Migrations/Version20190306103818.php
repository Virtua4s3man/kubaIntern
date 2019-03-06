<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190306103818 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, file VARCHAR(255) NOT NULL, alt VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product ADD cover_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD922726E9 FOREIGN KEY (cover_id) REFERENCES image (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D34A04AD922726E9 ON product (cover_id)');
        $this->addSql('DROP INDEX UNIQ_CDFC73565E237E06 ON product_category');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD922726E9');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP INDEX UNIQ_D34A04AD922726E9 ON product');
        $this->addSql('ALTER TABLE product DROP cover_id');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CDFC73565E237E06 ON product_category (name)');
    }
}
