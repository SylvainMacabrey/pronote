<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260704141736 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE examen (id INT AUTO_INCREMENT NOT NULL, date DATE NOT NULL, intitule VARCHAR(150) NOT NULL, affectation_id INT NOT NULL, INDEX IDX_514C8FEC6D0ABA22 (affectation_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE note (id INT AUTO_INCREMENT NOT NULL, valeur DOUBLE PRECISION NOT NULL, examen_id INT NOT NULL, eleve_id INT NOT NULL, INDEX IDX_CFBDFA145C8659A (examen_id), INDEX IDX_CFBDFA14A6CC7B2 (eleve_id), UNIQUE INDEX unique_note_eleve_examen (examen_id, eleve_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE examen ADD CONSTRAINT FK_514C8FEC6D0ABA22 FOREIGN KEY (affectation_id) REFERENCES affectation (id)');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA145C8659A FOREIGN KEY (examen_id) REFERENCES examen (id)');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA14A6CC7B2 FOREIGN KEY (eleve_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE examen DROP FOREIGN KEY FK_514C8FEC6D0ABA22');
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA145C8659A');
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA14A6CC7B2');
        $this->addSql('DROP TABLE examen');
        $this->addSql('DROP TABLE note');
    }
}
