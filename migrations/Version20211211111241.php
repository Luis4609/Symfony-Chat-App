<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211211111241 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE messages ADD from_user_id_id INT NOT NULL, ADD to_user_id_id INT NOT NULL, DROP from_user_id, DROP to_user_id');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E96B622A308 FOREIGN KEY (from_user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E96BAB58772 FOREIGN KEY (to_user_id_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_DB021E96B622A308 ON messages (from_user_id_id)');
        $this->addSql('CREATE INDEX IDX_DB021E96BAB58772 ON messages (to_user_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY FK_DB021E96B622A308');
        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY FK_DB021E96BAB58772');
        $this->addSql('DROP INDEX IDX_DB021E96B622A308 ON messages');
        $this->addSql('DROP INDEX IDX_DB021E96BAB58772 ON messages');
        $this->addSql('ALTER TABLE messages ADD from_user_id INT NOT NULL, ADD to_user_id INT NOT NULL, DROP from_user_id_id, DROP to_user_id_id');
    }
}
