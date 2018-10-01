<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180930162236 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sites (id INT UNSIGNED AUTO_INCREMENT NOT NULL, cust_id INT UNSIGNED NOT NULL, path VARCHAR(255) NOT NULL, INDEX IDX_BC00AA63BFF2A482 (cust_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE domains (id INT UNSIGNED AUTO_INCREMENT NOT NULL, cust_id INT UNSIGNED NOT NULL, site_id INT UNSIGNED DEFAULT NULL, fqdn VARCHAR(255) NOT NULL, is_blocked TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX ix_domain_customer (cust_id), INDEX ix_domain_site (site_id), UNIQUE INDEX ux_domain_fqdn (fqdn), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sites ADD CONSTRAINT FK_BC00AA63BFF2A482 FOREIGN KEY (cust_id) REFERENCES customers (id)');
        $this->addSql('ALTER TABLE domains ADD CONSTRAINT FK_8C7BBF9DBFF2A482 FOREIGN KEY (cust_id) REFERENCES customers (id)');
        $this->addSql('ALTER TABLE domains ADD CONSTRAINT FK_8C7BBF9DF6BD1646 FOREIGN KEY (site_id) REFERENCES sites (id)');
        $this->addSql('ALTER TABLE account_status RENAME INDEX uniq_5c34ab6fbff2a482 TO ux_customer_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE domains DROP FOREIGN KEY FK_8C7BBF9DF6BD1646');
        $this->addSql('DROP TABLE sites');
        $this->addSql('DROP TABLE domains');
        $this->addSql('ALTER TABLE account_status RENAME INDEX ux_customer_id TO UNIQ_5C34AB6FBFF2A482');
        $this->addSql('ALTER TABLE servers CHANGE type type VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE workers CHANGE department department VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE position position VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE status status VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
    }
}
