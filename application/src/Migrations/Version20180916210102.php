<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180916210102 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE workers CHANGE department department ENUM(\'support\', \'admin\', \'development\', \'manager\'), CHANGE position position ENUM(\'support\', \'admin\', \'developer\', \'manager\', \'devops\'), CHANGE status status ENUM(\'probation\', \'working\', \'fired\')');
        $this->addSql('ALTER TABLE customers CHANGE phone phone VARCHAR(35) NOT NULL COMMENT \'(DC2Type:phone_number)\'');
        $this->addSql('ALTER TABLE account_status ADD tokens_invalidation_date DATETIME DEFAULT NULL, ADD password_change_date DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE servers CHANGE type type ENUM(\'hosting\', \'system\', \'dedicated\', \'dedicated_with_maintanance\')');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE account_status DROP tokens_invalidation_date, DROP password_change_date');
        $this->addSql('ALTER TABLE customers CHANGE phone phone VARCHAR(20) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE servers CHANGE type type VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE workers CHANGE department department VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE position position VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE status status VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
    }
}
