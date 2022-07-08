<?php

declare(strict_types=1);

namespace App\Data\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220708135941 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE auth_users (id UUID NOT NULL, email VARCHAR(255) NOT NULL, date DATE NOT NULL, status VARCHAR(16) NOT NULL, role VARCHAR(16) NOT NULL, new_email VARCHAR(255) DEFAULT NULL, password_hash VARCHAR(255) DEFAULT NULL, join_confirm_token_value VARCHAR(255) DEFAULT NULL, join_confirm_token_expires DATE DEFAULT NULL, password_reset_token_value VARCHAR(255) DEFAULT NULL, password_reset_token_expires DATE DEFAULT NULL, new_email_token_value VARCHAR(255) DEFAULT NULL, new_email_token_expires DATE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D8A1F49CE7927C74 ON auth_users (email)');
        $this->addSql('COMMENT ON COLUMN auth_users.id IS \'(DC2Type:auth_user_id)\'');
        $this->addSql('COMMENT ON COLUMN auth_users.email IS \'(DC2Type:auth_user_email)\'');
        $this->addSql('COMMENT ON COLUMN auth_users.date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN auth_users.status IS \'(DC2Type:auth_user_status)\'');
        $this->addSql('COMMENT ON COLUMN auth_users.role IS \'(DC2Type:auth_user_role)\'');
        $this->addSql('COMMENT ON COLUMN auth_users.new_email IS \'(DC2Type:auth_user_email)\'');
        $this->addSql('COMMENT ON COLUMN auth_users.join_confirm_token_expires IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN auth_users.password_reset_token_expires IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN auth_users.new_email_token_expires IS \'(DC2Type:date_immutable)\'');
        $this->addSql('CREATE TABLE auth_users_networks (id UUID NOT NULL, user_id UUID NOT NULL, network_name VARCHAR(16) NOT NULL, network_identity VARCHAR(16) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BF481C49A76ED395 ON auth_users_networks (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BF481C49257EBD71C756D255 ON auth_users_networks (network_name, network_identity)');
        $this->addSql('COMMENT ON COLUMN auth_users_networks.user_id IS \'(DC2Type:auth_user_id)\'');
        $this->addSql('ALTER TABLE auth_users_networks ADD CONSTRAINT FK_BF481C49A76ED395 FOREIGN KEY (user_id) REFERENCES auth_users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE auth_users_networks DROP CONSTRAINT FK_BF481C49A76ED395');
        $this->addSql('DROP TABLE auth_users');
        $this->addSql('DROP TABLE auth_users_networks');
    }
}
