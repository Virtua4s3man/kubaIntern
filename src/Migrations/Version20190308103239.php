<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190308103239 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE book ADD cover_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A331922726E9 FOREIGN KEY (cover_id) REFERENCES image (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CBE5A331922726E9 ON book (cover_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CDFC73565E237E06 ON product_category (name)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A331922726E9');
        $this->addSql('DROP INDEX UNIQ_CBE5A331922726E9 ON book');
        $this->addSql('ALTER TABLE book DROP cover_id');
        $this->addSql('DROP INDEX UNIQ_CDFC73565E237E06 ON product_category');
    }
}
