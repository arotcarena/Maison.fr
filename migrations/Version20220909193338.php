<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220909193338 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pro (id INT AUTO_INCREMENT NOT NULL, business_name VARCHAR(255) NOT NULL, contact_name VARCHAR(255) NOT NULL, phone VARCHAR(10) NOT NULL, departments LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pro_category (pro_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_17B8BB5BC3B7E4BA (pro_id), INDEX IDX_17B8BB5B12469DE2 (category_id), PRIMARY KEY(pro_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pro_category ADD CONSTRAINT FK_17B8BB5BC3B7E4BA FOREIGN KEY (pro_id) REFERENCES pro (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pro_category ADD CONSTRAINT FK_17B8BB5B12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pro_category DROP FOREIGN KEY FK_17B8BB5BC3B7E4BA');
        $this->addSql('ALTER TABLE pro_category DROP FOREIGN KEY FK_17B8BB5B12469DE2');
        $this->addSql('DROP TABLE pro');
        $this->addSql('DROP TABLE pro_category');
    }
}
