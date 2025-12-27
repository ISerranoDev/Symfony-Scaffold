<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251227183229 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE roles (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(180) NOT NULL, etiqueta VARCHAR(180) NOT NULL, UNIQUE INDEX UNIQ_B63E2EC73A909126 (nombre), UNIQUE INDEX UNIQ_B63E2EC76D5CA63A (etiqueta), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('CREATE TABLE ticket_messages (id VARCHAR(50) NOT NULL, mensaje LONGTEXT NOT NULL, fecha_creacion DATETIME NOT NULL, ticket_id VARCHAR(50) NOT NULL, usuario_id VARCHAR(50) NOT NULL, INDEX IDX_5E6BE217700047D2 (ticket_id), INDEX IDX_5E6BE217DB38439E (usuario_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('CREATE TABLE tickets (id VARCHAR(50) NOT NULL, titulo LONGTEXT NOT NULL, descripcion LONGTEXT NOT NULL, cerrado TINYINT(1) DEFAULT 0 NOT NULL, fecha_creacion DATETIME NOT NULL, fecha_cerrado DATETIME DEFAULT NULL, usuario_id VARCHAR(50) DEFAULT NULL, INDEX IDX_54469DF4DB38439E (usuario_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('CREATE TABLE usuario (id VARCHAR(50) NOT NULL, email VARCHAR(255) NOT NULL, contrasena VARCHAR(255) NOT NULL, activo TINYINT(1) DEFAULT 1 NOT NULL, fecha_creacion DATETIME NOT NULL, fecha_alta DATETIME DEFAULT NULL, fecha_baja DATETIME DEFAULT NULL, codigo_recuperacion VARCHAR(255) DEFAULT NULL, fecha_expiracion_recuperacion DATETIME DEFAULT NULL, nombre LONGTEXT DEFAULT NULL, apellido_1 LONGTEXT DEFAULT NULL, apellido_2 LONGTEXT DEFAULT NULL, usuario_modificado_id VARCHAR(50) DEFAULT NULL, UNIQUE INDEX UNIQ_2265B05DE7927C74 (email), INDEX IDX_2265B05D28FD38C6 (usuario_modificado_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('CREATE TABLE usuario_has_roles (id VARCHAR(50) NOT NULL, usuario_id VARCHAR(50) NOT NULL, rol_id INT NOT NULL, INDEX IDX_C48CD46BDB38439E (usuario_id), INDEX IDX_C48CD46B4BAB96C (rol_id), UNIQUE INDEX user_has_role_unique (usuario_id, rol_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('ALTER TABLE ticket_messages ADD CONSTRAINT FK_5E6BE217700047D2 FOREIGN KEY (ticket_id) REFERENCES tickets (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ticket_messages ADD CONSTRAINT FK_5E6BE217DB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tickets ADD CONSTRAINT FK_54469DF4DB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE usuario ADD CONSTRAINT FK_2265B05D28FD38C6 FOREIGN KEY (usuario_modificado_id) REFERENCES usuario (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE usuario_has_roles ADD CONSTRAINT FK_C48CD46BDB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE usuario_has_roles ADD CONSTRAINT FK_C48CD46B4BAB96C FOREIGN KEY (rol_id) REFERENCES roles (id) ON DELETE CASCADE');

        $this->addSql("
                INSERT INTO roles (id, nombre, etiqueta) VALUES
                (1, 'ROLE_ADMIN', 'Administrador'),
                (2, 'ROLE_USER', 'Usuario')
        ");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ticket_messages DROP FOREIGN KEY FK_5E6BE217700047D2');
        $this->addSql('ALTER TABLE ticket_messages DROP FOREIGN KEY FK_5E6BE217DB38439E');
        $this->addSql('ALTER TABLE tickets DROP FOREIGN KEY FK_54469DF4DB38439E');
        $this->addSql('ALTER TABLE usuario DROP FOREIGN KEY FK_2265B05D28FD38C6');
        $this->addSql('ALTER TABLE usuario_has_roles DROP FOREIGN KEY FK_C48CD46BDB38439E');
        $this->addSql('ALTER TABLE usuario_has_roles DROP FOREIGN KEY FK_C48CD46B4BAB96C');
        $this->addSql('DROP TABLE roles');
        $this->addSql('DROP TABLE ticket_messages');
        $this->addSql('DROP TABLE tickets');
        $this->addSql('DROP TABLE usuario');
        $this->addSql('DROP TABLE usuario_has_roles');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
