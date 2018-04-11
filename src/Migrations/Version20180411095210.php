<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180411095210 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE poll_answers (poll_id INT NOT NULL, answer_id INT NOT NULL, INDEX IDX_AC854B393C947C0F (poll_id), UNIQUE INDEX UNIQ_AC854B39AA334807 (answer_id), PRIMARY KEY(poll_id, answer_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vote (uid INT AUTO_INCREMENT NOT NULL, answer_id INT DEFAULT NULL, user VARCHAR(64) NOT NULL, INDEX IDX_5A108564AA334807 (answer_id), PRIMARY KEY(uid)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE answer (uid INT AUTO_INCREMENT NOT NULL, title VARCHAR(100) NOT NULL, PRIMARY KEY(uid)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE poll_answers ADD CONSTRAINT FK_AC854B393C947C0F FOREIGN KEY (poll_id) REFERENCES poll (uid)');
        $this->addSql('ALTER TABLE poll_answers ADD CONSTRAINT FK_AC854B39AA334807 FOREIGN KEY (answer_id) REFERENCES answer (uid)');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A108564AA334807 FOREIGN KEY (answer_id) REFERENCES answer (uid)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE poll_answers DROP FOREIGN KEY FK_AC854B39AA334807');
        $this->addSql('ALTER TABLE vote DROP FOREIGN KEY FK_5A108564AA334807');
        $this->addSql('DROP TABLE poll_answers');
        $this->addSql('DROP TABLE vote');
        $this->addSql('DROP TABLE answer');
    }
}
