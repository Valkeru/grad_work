<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180816095913 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE customers (id INT UNSIGNED AUTO_INCREMENT NOT NULL, login VARCHAR(255) NOT NULL, name VARCHAR(200) NOT NULL, email VARCHAR(200) NOT NULL, phone VARCHAR(20) NOT NULL, password VARCHAR(95) NOT NULL, UNIQUE INDEX ux_cust_login (login), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE workers (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE account_status (id INT UNSIGNED AUTO_INCREMENT NOT NULL, cust_id INT UNSIGNED NOT NULL, is_blocked TINYINT(1) DEFAULT \'0\' NOT NULL, registration_date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX ux_cust_id (cust_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE account_status ADD CONSTRAINT fk_customer_status FOREIGN KEY (cust_id) REFERENCES customers (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE account_status DROP FOREIGN KEY fk_customer_status');
        $this->addSql('DROP TABLE customers');
        $this->addSql('DROP TABLE workers');
        $this->addSql('DROP TABLE account_status');
    }
}
