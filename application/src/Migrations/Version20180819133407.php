<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180819133407 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE workers (id INT AUTO_INCREMENT NOT NULL, login VARCHAR(255) NOT NULL, password VARCHAR(95) NOT NULL, email VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, department ENUM(\'support\', \'admin\', \'development\', \'manager\', \'bill\'), position ENUM(\'support\', \'admin\', \'developer\', \'manager\', \'devops\'), status ENUM(\'probation\', \'working\', \'fired\'), is_admin TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX idx_name (name), UNIQUE INDEX ux_login (login), UNIQUE INDEX ux_email (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customers (id INT UNSIGNED AUTO_INCREMENT NOT NULL, login VARCHAR(255) NOT NULL, name VARCHAR(200) NOT NULL, email VARCHAR(200) NOT NULL, phone VARCHAR(20) NOT NULL, password VARCHAR(95) NOT NULL, UNIQUE INDEX UNIQ_62534E21AA08CB10 (login), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE account_status (id INT UNSIGNED AUTO_INCREMENT NOT NULL, cust_id INT UNSIGNED DEFAULT NULL, server_id INT UNSIGNED DEFAULT NULL, is_blocked TINYINT(1) DEFAULT \'0\' NOT NULL, registration_date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX UNIQ_5C34AB6FBFF2A482 (cust_id), INDEX IDX_5C34AB6F1844E6B7 (server_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE servers (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, internal_ip VARCHAR(15) NOT NULL, main_ip VARCHAR(15) NOT NULL, ssl_ip VARCHAR(15) NOT NULL, outgoing_ip VARCHAR(15) NOT NULL, type ENUM(\'hosting\', \'system\', \'dedicated\', \'dedicated_with_maintanance\'), registration_enabled TINYINT(1) DEFAULT \'0\' NOT NULL, UNIQUE INDEX ux_server_name (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE account_status ADD CONSTRAINT FK_5C34AB6FBFF2A482 FOREIGN KEY (cust_id) REFERENCES customers (id)');
        $this->addSql('ALTER TABLE account_status ADD CONSTRAINT FK_5C34AB6F1844E6B7 FOREIGN KEY (server_id) REFERENCES servers (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE account_status DROP FOREIGN KEY FK_5C34AB6FBFF2A482');
        $this->addSql('ALTER TABLE account_status DROP FOREIGN KEY FK_5C34AB6F1844E6B7');
        $this->addSql('DROP TABLE workers');
        $this->addSql('DROP TABLE customers');
        $this->addSql('DROP TABLE account_status');
        $this->addSql('DROP TABLE servers');
    }
}
