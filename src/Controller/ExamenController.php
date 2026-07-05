<?php

namespace App\Controller;

use App\Entity\Examen;
use App\Repository\AffectationRepository;
use App\Repository\ExamenRepository;
use App\Security\Voter\AffectationVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ExamenController extends AbstractController
{
    #[Route('/api/examens', name: 'app_examen_create', methods: ['POST'])]
    public function create(
        Request $request,
        AffectationRepository $affectationRepository,
        EntityManagerInterface $em,
        ValidatorInterface $validator,
    ): JsonResponse {
        $payload = json_decode($request->getContent(), true);

        $affectation = $affectationRepository->find($payload['affectationId'] ?? null);

        if (!$affectation) {
            return $this->json(['error' => 'Affectation introuvable.'], 404);
        }

        // Vérifie que le prof connecté possède bien cette affectation
        $this->denyAccessUnlessGranted(AffectationVoter::GERER, $affectation);

        $examen = new Examen();
        $examen->setAffectation($affectation);
        $examen->setIntitule($payload['intitule'] ?? '');
        $examen->setDate(new \DateTime($payload['dateExamen'] ?? 'now'));

        $erreurs = $validator->validate($examen);
        if (count($erreurs) > 0) {
            return $this->json(['errors' => (string) $erreurs], 400);
        }

        $em->persist($examen);
        $em->flush();

        return $this->json([
            'id' => $examen->getId(),
            'intitule' => $examen->getIntitule(),
            'dateExamen' => $examen->getDate()->format('d/m/Y'),
            'class_room' => $affectation->getClassRoom()->getNom(),
            'matiere' => $affectation->getMatiere()->getNom(),
        ], 201);
    }

    #[Route('/api/examens/{id}/eleves', name: 'app_examen_eleves', methods: ['GET'])]
    public function eleves(int $id, ExamenRepository $examenRepository): JsonResponse
    {
        $examen = $examenRepository->find($id);

        if (!$examen) {
            return $this->json(['error' => 'Examen introuvable.'], 404);
        }

        $this->denyAccessUnlessGranted(AffectationVoter::GERER, $examen->getAffectation());

        $eleves = $examen->getAffectation()->getClassRoom()->getEleves();

        $data = array_map(
            fn($eleve) => ['id' => $eleve->getId(), 'nom' => $eleve->getNom(), 'prenom' => $eleve->getPrenom()],
            $eleves->toArray()
        );

        return $this->json($data);
    }

    #[Route('/api/examens/{id}', name: 'app_examen_show', methods: ['GET'])]
    public function show(int $id, ExamenRepository $examenRepository): JsonResponse
    {
        $examen = $examenRepository->find($id);

        if (!$examen) {
            return $this->json(['error' => 'Examen introuvable.'], 404);
        }

        $this->denyAccessUnlessGranted(AffectationVoter::GERER, $examen->getAffectation());

        return $this->json([
            'id' => $examen->getId(),
            'intitule' => $examen->getIntitule(),
            'dateExamen' => $examen->getDate()->format('d/m/Y'),
            'affectationId' => $examen->getAffectation()->getId(),
            'classe' => $examen->getAffectation()->getClasse()->getNom(),
            'matiere' => $examen->getAffectation()->getMatiere()->getNom(),
        ]);
    }

    #[Route('/api/examens/{id}', name: 'app_examen_update', methods: ['PATCH'])]
    public function update(
        int $id,
        Request $request,
        ExamenRepository $examenRepository,
        EntityManagerInterface $em,
        ValidatorInterface $validator,
    ): JsonResponse {
        $examen = $examenRepository->find($id);

        if (!$examen) {
            return $this->json(['error' => 'Examen introuvable.'], 404);
        }

        $this->denyAccessUnlessGranted(AffectationVoter::GERER, $examen->getAffectation());

        $payload = json_decode($request->getContent(), true);

        if (isset($payload['intitule'])) {
            $examen->setIntitule($payload['intitule']);
        }
        if (isset($payload['dateExamen'])) {
            $examen->setDateExamen(new \DateTime($payload['dateExamen']));
        }

        $erreurs = $validator->validate($examen);
        if (count($erreurs) > 0) {
            return $this->json(['errors' => (string) $erreurs], 400);
        }

        $em->flush();

        return $this->json([
            'id' => $examen->getId(),
            'intitule' => $examen->getIntitule(),
            'dateExamen' => $examen->getDate()->format('d/m/Y'),
        ]);
    }
}
