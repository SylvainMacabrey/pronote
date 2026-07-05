<?php

namespace App\DataFixtures;

use App\Entity\Affectation;
use App\Entity\ClassRoom;
use App\Entity\Eleve;
use App\Entity\Matiere;
use App\Entity\Professeur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private const NIVEAUX = ['6', '5', '4', '3', '2', '1', 'T'];
    private const LETTRES = ['A', 'B', 'C', 'D'];

    private const MATIERES = [
        'Mathématiques',
        'Français',
        'Histoire-Géographie',
        'Anglais',
        'Espagnol',
        'SVT',
        'Physique-Chimie',
        'EPS',
        'Technologie',
        'Arts Plastiques',
        'Éducation Musicale',
        'SES',
    ];

    public function __construct(
        private readonly UserPasswordHasherInterface $hasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // 1. Classes (6A, 6B... TD)
        $classRooms = [];
        foreach (self::NIVEAUX as $niveau) {
            foreach (self::LETTRES as $lettre) {
                $classRoom = new ClassRoom();
                $classRoom->setNom($niveau . $lettre);
                $manager->persist($classRoom);
                $classRooms[] = $classRoom;
            }
        }

        // 2. Matières
        $matieres = [];
        foreach (self::MATIERES as $nomMatiere) {
            $matiere = new Matiere();
            $matiere->setNom($nomMatiere);
            $manager->persist($matiere);
            $matieres[] = $matiere;
        }

        // 3. Professeurs (2 par matière) + affectations réparties sur les classes
        $compteurProf = 1;
        foreach ($matieres as $matiere) {
            $slug = $this->slugify($matiere->getNom());

            $prof1 = $this->creerProfesseur($manager, $faker, $slug . '.a');
            $prof2 = $this->creerProfesseur($manager, $faker, $slug . '.b');

            // On coupe la liste des 28 classes en deux : 14 pour chaque prof
            $milieu = intdiv(count($classRooms), 2);
            $classRoomsProf1 = array_slice($classRooms, 0, $milieu);
            $classRoomsProf2 = array_slice($classRooms, $milieu);

            foreach ($classRoomsProf1 as $classRoom) {
                $this->creerAffectation($manager, $prof1, $matiere, $classRoom);
            }
            foreach ($classRoomsProf2 as $classRoom) {
                $this->creerAffectation($manager, $prof2, $matiere, $classRoom);
            }

            $compteurProf += 2;
        }

        // 4. Élèves (10 par classe)
        foreach ($classRooms as $classRoom) {
            for ($i = 1; $i <= 10; $i++) {
                $eleve = new Eleve();
                $prenom = $faker->firstName();
                $nom = $faker->lastName();

                $eleve->setPrenom($prenom);
                $eleve->setNom($nom);
                $eleve->setEmail($this->genererEmail($prenom, $nom, $classRoom->getNom()));
                $eleve->setPassword($this->hasher->hashPassword($eleve, 'password'));
                $eleve->setclassRoom($classRoom);

                $manager->persist($eleve);
            }
        }

        $manager->flush();
    }

    private function creerProfesseur(ObjectManager $manager, \Faker\Generator $faker, string $identifiantUnique): Professeur
    {
        $prof = new Professeur();
        $prenom = $faker->firstName();
        $nom = $faker->lastName();

        $prof->setPrenom($prenom);
        $prof->setNom($nom);
        $prof->setEmail('prof.' . $identifiantUnique . '@pronote.fr');
        $prof->setPassword($this->hasher->hashPassword($prof, 'password'));

        $manager->persist($prof);

        return $prof;
    }

    private function creerAffectation(ObjectManager $manager, Professeur $prof, Matiere $matiere, ClassRoom $classRoom): void
    {
        $affectation = new Affectation();
        $affectation->setProfesseur($prof);
        $affectation->setMatiere($matiere);
        $affectation->setClassRoom($classRoom);

        $manager->persist($affectation);
    }

    private function slugify(string $texte): string
    {
        $texte = iconv('UTF-8', 'ASCII//TRANSLIT', $texte);
        $texte = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $texte));

        return trim($texte, '-');
    }

    private function genererEmail(string $prenom, string $nom, string $classRoom): string
    {
        $base = $this->slugify($prenom . '.' . $nom);

        return $base . '.' . strtolower($classRoom) . '@eleve.pronote.fr';
    }
}
