<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181002081521 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE db_accesses (id INT UNSIGNED AUTO_INCREMENT NOT NULL, db_id INT UNSIGNED DEFAULT NULL, host VARCHAR(255) DEFAULT \'localhost\' NOT NULL, INDEX ix_access_db (db_id), UNIQUE INDEX ux_db_host (db_id, host), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `databases` (id INT UNSIGNED AUTO_INCREMENT NOT NULL, cust_id INT UNSIGNED NOT NULL, suffix VARCHAR(255) NOT NULL, INDEX ix_database_suffix (suffix), INDEX ix_cust_id (cust_id), UNIQUE INDEX ux_dadabase_suffix (suffix, cust_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE db_accesses ADD CONSTRAINT FK_74651B37A2BF053A FOREIGN KEY (db_id) REFERENCES `databases` (id)');
        $this->addSql('ALTER TABLE `databases` ADD CONSTRAINT FK_C71191C2BFF2A482 FOREIGN KEY (cust_id) REFERENCES customers (id)');
        $this->addSql('ALTER TABLE customers RENAME INDEX idx_62534e211844e6b7 TO ix_customer_server');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE db_accesses DROP FOREIGN KEY FK_74651B37A2BF053A');
        $this->addSql('DROP TABLE db_accesses');
        $this->addSql('DROP TABLE `databases`');
        $this->addSql('ALTER TABLE customers RENAME INDEX ix_customer_server TO IDX_62534E211844E6B7');
    }
}
