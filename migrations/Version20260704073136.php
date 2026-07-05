<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260704073136 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE affectation (id INT AUTO_INCREMENT NOT NULL, professeur_id INT NOT NULL, matiere_id INT NOT NULL, class_room_id INT NOT NULL, INDEX IDX_F4DD61D3BAB22EE9 (professeur_id), INDEX IDX_F4DD61D3F46CD258 (matiere_id), INDEX IDX_F4DD61D39162176F (class_room_id), UNIQUE INDEX unique_affectation (professeur_id, matiere_id, class_room_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE class_room (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE matiere (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, class_room_id INT NOT NULL, INDEX IDX_8D93D6499162176F (class_room_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE affectation ADD CONSTRAINT FK_F4DD61D3BAB22EE9 FOREIGN KEY (professeur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE affectation ADD CONSTRAINT FK_F4DD61D3F46CD258 FOREIGN KEY (matiere_id) REFERENCES matiere (id)');
        $this->addSql('ALTER TABLE affectation ADD CONSTRAINT FK_F4DD61D39162176F FOREIGN KEY (class_room_id) REFERENCES class_room (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6499162176F FOREIGN KEY (class_room_id) REFERENCES class_room (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE affectation DROP FOREIGN KEY FK_F4DD61D3BAB22EE9');
        $this->addSql('ALTER TABLE affectation DROP FOREIGN KEY FK_F4DD61D3F46CD258');
        $this->addSql('ALTER TABLE affectation DROP FOREIGN KEY FK_F4DD61D39162176F');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6499162176F');
        $this->addSql('DROP TABLE affectation');
        $this->addSql('DROP TABLE class_room');
        $this->addSql('DROP TABLE matiere');
        $this->addSql('DROP TABLE user');
    }
}
