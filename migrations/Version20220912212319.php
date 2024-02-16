<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220912212319 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD pro_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649C3B7E4BA FOREIGN KEY (pro_id) REFERENCES pro (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649C3B7E4BA ON user (pro_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649C3B7E4BA');
        $this->addSql('DROP INDEX UNIQ_8D93D649C3B7E4BA ON user');
        $this->addSql('ALTER TABLE user DROP pro_id');
    }
}
