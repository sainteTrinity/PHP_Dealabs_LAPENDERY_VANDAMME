<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230620072536 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE deal_user (deal_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_3BEEB3E6F60E2305 (deal_id), INDEX IDX_3BEEB3E6A76ED395 (user_id), PRIMARY KEY(deal_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE deal_user ADD CONSTRAINT FK_3BEEB3E6F60E2305 FOREIGN KEY (deal_id) REFERENCES deal (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE deal_user ADD CONSTRAINT FK_3BEEB3E6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE deal_user DROP FOREIGN KEY FK_3BEEB3E6F60E2305');
        $this->addSql('ALTER TABLE deal_user DROP FOREIGN KEY FK_3BEEB3E6A76ED395');
        $this->addSql('DROP TABLE deal_user');
    }
}
