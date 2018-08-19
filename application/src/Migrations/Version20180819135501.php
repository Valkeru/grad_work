<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180819135501 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE workers CHANGE department department ENUM(\'support\', \'admin\', \'development\', \'manager\', \'bill\'), CHANGE position position ENUM(\'support\', \'admin\', \'developer\', \'manager\', \'devops\'), CHANGE status status ENUM(\'probation\', \'working\', \'fired\')');
        $this->addSql('ALTER TABLE customers ADD server_id INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE account_status DROP FOREIGN KEY FK_5C34AB6F1844E6B7');
        $this->addSql('DROP INDEX IDX_5C34AB6F1844E6B7 ON account_status');
        $this->addSql('ALTER TABLE account_status DROP server_id');
        $this->addSql('ALTER TABLE servers CHANGE type type ENUM(\'hosting\', \'system\', \'dedicated\', \'dedicated_with_maintanance\')');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE account_status ADD server_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE account_status ADD CONSTRAINT FK_5C34AB6F1844E6B7 FOREIGN KEY (server_id) REFERENCES servers (id)');
        $this->addSql('CREATE INDEX IDX_5C34AB6F1844E6B7 ON account_status (server_id)');
        $this->addSql('ALTER TABLE customers DROP server_id');
        $this->addSql('ALTER TABLE servers CHANGE type type VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE workers CHANGE department department VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE position position VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE status status VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
    }
}
