<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231221202321 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE roles (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(180) NOT NULL, label VARCHAR(180) NOT NULL, UNIQUE INDEX UNIQ_B63E2EC75E237E06 (name), UNIQUE INDEX UNIQ_B63E2EC7EA750E8 (label), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ticket_messages (id INT AUTO_INCREMENT NOT NULL, ticket_id INT NOT NULL, user_id INT NOT NULL, message LONGTEXT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_5E6BE217700047D2 (ticket_id), INDEX IDX_5E6BE217A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tickets (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, title LONGTEXT NOT NULL, message LONGTEXT NOT NULL, closed TINYINT(1) DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, closed_at DATETIME DEFAULT NULL, INDEX IDX_54469DF4A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_has_roles (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, role_id INT NOT NULL, INDEX IDX_4D57B39DA76ED395 (user_id), INDEX IDX_4D57B39DD60322AC (role_id), UNIQUE INDEX user_has_role_unique (user_id, role_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, enabled TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, entry_date DATETIME DEFAULT NULL, leaving_date DATETIME DEFAULT NULL, recover_code VARCHAR(255) DEFAULT NULL, recover_expiration_date DATETIME DEFAULT NULL, name VARCHAR(180) DEFAULT NULL, surname_1 VARCHAR(180) DEFAULT NULL, surname_2 VARCHAR(180) DEFAULT NULL, UNIQUE INDEX UNIQ_1483A5E9F85E0677 (username), UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ticket_messages ADD CONSTRAINT FK_5E6BE217700047D2 FOREIGN KEY (ticket_id) REFERENCES tickets (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ticket_messages ADD CONSTRAINT FK_5E6BE217A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tickets ADD CONSTRAINT FK_54469DF4A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_has_roles ADD CONSTRAINT FK_4D57B39DA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_has_roles ADD CONSTRAINT FK_4D57B39DD60322AC FOREIGN KEY (role_id) REFERENCES roles (id) ON DELETE CASCADE');

        $this->addSql("
            INSERT INTO `roles`(`id`, `name`, `label`) VALUES 
                (1, 'ROLE_ADMIN', 'Gestor'),
                (2, 'ROLE_USER', 'Usuario')
        ");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("DELETE FROM `roles` WHERE ID IN(1,2)");

        $this->addSql('ALTER TABLE ticket_messages DROP FOREIGN KEY FK_5E6BE217700047D2');
        $this->addSql('ALTER TABLE ticket_messages DROP FOREIGN KEY FK_5E6BE217A76ED395');
        $this->addSql('ALTER TABLE tickets DROP FOREIGN KEY FK_54469DF4A76ED395');
        $this->addSql('ALTER TABLE user_has_roles DROP FOREIGN KEY FK_4D57B39DA76ED395');
        $this->addSql('ALTER TABLE user_has_roles DROP FOREIGN KEY FK_4D57B39DD60322AC');
        $this->addSql('DROP TABLE roles');
        $this->addSql('DROP TABLE ticket_messages');
        $this->addSql('DROP TABLE tickets');
        $this->addSql('DROP TABLE user_has_roles');
        $this->addSql('DROP TABLE users');
    }
}
