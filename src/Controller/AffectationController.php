<?php

namespace App\Controller;

use App\Entity\Professeur;
use App\Repository\AffectationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class AffectationController extends AbstractController
{
    #[Route('/api/mes-affectations', name: 'app_mes_affectations', methods: ['GET'])]
    public function index(Security $security): JsonResponse
    {
        $professeur = $security->getUser();

        if (!$professeur instanceof Professeur) {
            return $this->json(['error' => 'Réservé aux professeurs.'], 403);
        }

        $data = array_map(
            fn($affectation) => [
                'id' => $affectation->getId(),
                'class_room' => $affectation->getClassRoom()->getNom(),
                'matiere' => $affectation->getMatiere()->getNom(),
            ],
            $professeur->getAffectations()->toArray()
        );

        return $this->json($data);
    }

    #[Route('/api/mes-classes/{nomClasse}/donnees', name: 'app_classe_donnees', methods: ['GET'])]
    public function donneesClasse(string $nomClasse, Security $security): JsonResponse
    {
        $professeur = $security->getUser();

        if (!$professeur instanceof Professeur) {
            return $this->json(['error' => 'Réservé aux professeurs.'], 403);
        }

        $donnees = [];

        foreach ($professeur->getAffectations() as $affectation) {
            if ($affectation->getClassRoom()->getNom() !== $nomClasse) {
                continue;
            }

            $examens = array_map(function ($examen) {
                $notes = array_map(fn($note) => [
                    'eleveId' => $note->getEleve()->getId(),
                    'eleve' => $note->getEleve()->getPrenom() . ' ' . $note->getEleve()->getNom(),
                    'valeur' => $note->getValeur(),
                ], $examen->getNotes()->toArray());

                $moyenne = count($notes) > 0
                    ? round(array_sum(array_column($notes, 'valeur')) / count($notes), 2)
                    : null;

                return [
                    'id' => $examen->getId(),
                    'intitule' => $examen->getIntitule(),
                    'dateExamen' => $examen->getDate()->format('d/m/Y'),
                    'notes' => $notes,
                    'moyenneClasse' => $moyenne,
                ];
            }, $affectation->getExamens()->toArray());

            $donnees[] = [
                'id' => $affectation->getId(),
                'classe' => $affectation->getClassRoom()->getNom(),
                'matiere' => $affectation->getMatiere()->getNom(),
                'examens' => $examens,
            ];
        }

        return $this->json($donnees);
    }

    #[Route('/api/affectations/{id}/examens', name: 'app_affectation_examens', methods: ['GET'])]
    public function examens(int $id, AffectationRepository $affectationRepository): JsonResponse
    {
        $affectation = $affectationRepository->find($id);

        if (!$affectation) {
            return $this->json(['error' => 'Affectation introuvable.'], 404);
        }

        $this->denyAccessUnlessGranted(\App\Security\Voter\AffectationVoter::GERER, $affectation);

        $data = array_map(
            fn($examen) => [
                'id' => $examen->getId(),
                'intitule' => $examen->getIntitule(),
                'dateExamen' => $examen->getDate()->format('d/m/Y'),
            ],
            $affectation->getExamens()->toArray()
        );

        return $this->json($data);
    }

    #[Route('/api/mes-classes', name: 'app_mes_classes', methods: ['GET'])]
    public function mesClasses(Security $security): JsonResponse
    {
        $professeur = $security->getUser();

        if (!$professeur instanceof Professeur) {
            return $this->json(['error' => 'Réservé aux professeurs.'], 403);
        }

        $noms = [];
        foreach ($professeur->getAffectations() as $affectation) {
            $nom = $affectation->getClassRoom()->getNom();
            if (!in_array($nom, $noms, true)) {
                $noms[] = $nom;
            }
        }

        return $this->json($noms);
    }
}
