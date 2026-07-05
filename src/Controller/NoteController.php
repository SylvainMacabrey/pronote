<?php

namespace App\Controller;

use App\Entity\Note;
use App\Repository\EleveRepository;
use App\Repository\ExamenRepository;
use App\Security\Voter\AffectationVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class NoteController extends AbstractController
{
    #[Route('/api/examens/{id}/notes', name: 'app_note_save', methods: ['POST'])]
    public function save(
        int $id,
        Request $request,
        ExamenRepository $examenRepository,
        EleveRepository $eleveRepository,
        EntityManagerInterface $em,
        ValidatorInterface $validator,
    ): JsonResponse {
        $examen = $examenRepository->find($id);

        if (!$examen) {
            return $this->json(['error' => 'Examen introuvable.'], 404);
        }

        // Vérifie que le prof connecté est bien celui qui a créé cet examen
        $this->denyAccessUnlessGranted(AffectationVoter::GERER, $examen->getAffectation());

        $payload = json_decode($request->getContent(), true);
        $erreursGlobales = [];

        foreach ($payload['notes'] ?? [] as $ligne) {
            $eleve = $eleveRepository->find($ligne['eleveId'] ?? null);

            if (!$eleve || $eleve->getClassRoom() !== $examen->getAffectation()->getClassRoom()) {
                $erreursGlobales[] = sprintf('Élève invalide (id: %s)', $ligne['eleveId'] ?? '?');
                continue;
            }

            // Cherche une note existante pour cet élève+examen, sinon en crée une
            $note = $em->getRepository(Note::class)->findOneBy([
                'examen' => $examen,
                'eleve' => $eleve,
            ]) ?? new Note();

            $note->setExamen($examen);
            $note->setEleve($eleve);
            $note->setValeur((float) $ligne['valeur']);

            $erreurs = $validator->validate($note);
            if (count($erreurs) > 0) {
                $erreursGlobales[] = sprintf('Note invalide pour élève %d : %s', $eleve->getId(), (string) $erreurs);
                continue;
            }

            $em->persist($note);
        }

        if (!empty($erreursGlobales)) {
            return $this->json(['errors' => $erreursGlobales], 400);
        }

        $em->flush();

        return $this->json(['message' => 'Notes enregistrées.']);
    }

    #[Route('/api/examens/{id}/notes', name: 'app_note_list', methods: ['GET'])]
    public function list(int $id, ExamenRepository $examenRepository): JsonResponse
    {
        $examen = $examenRepository->find($id);

        if (!$examen) {
            return $this->json(['error' => 'Examen introuvable.'], 404);
        }

        $this->denyAccessUnlessGranted(AffectationVoter::GERER, $examen->getAffectation());

        $notes = $examen->getNotes();

        $donnees = array_map(
            fn($note) => [
                'eleveId' => $note->getEleve()->getId(),
                'eleve' => $note->getEleve()->getPrenom() . ' ' . $note->getEleve()->getNom(),
                'valeur' => $note->getValeur(),
            ],
            $notes->toArray()
        );

        $moyenne = count($donnees) > 0
            ? round(array_sum(array_column($donnees, 'valeur')) / count($donnees), 2)
            : null;

        return $this->json([
            'examen' => $examen->getIntitule(),
            'class_room' => $examen->getAffectation()->getClassRoom()->getNom(),
            'notes' => $donnees,
            'moyenne' => $moyenne,
        ]);
    }

    #[Route('/api/mes-notes', name: 'app_mes_notes', methods: ['GET'])]
    public function mesNotes(Security $security): JsonResponse
    {
        $eleve = $security->getUser();

        if (!$eleve instanceof \App\Entity\Eleve) {
            return $this->json(['error' => 'Réservé aux élèves.'], 403);
        }

        $parMatiere = [];

        foreach ($eleve->getNotes() as $note) {
            $examen = $note->getExamen();
            $matiere = $examen->getAffectation()->getMatiere()->getNom();

            $toutesLesNotes = array_map(fn($n) => $n->getValeur(), $examen->getNotes()->toArray());
            $moyenneClasseExamen = count($toutesLesNotes) > 0
                ? round(array_sum($toutesLesNotes) / count($toutesLesNotes), 2)
                : null;

            $parMatiere[$matiere][] = [
                'examen' => $examen->getIntitule(),
                'date' => $examen->getDate()->format('d/m/Y'),
                'valeur' => $note->getValeur(),
                'moyenneClasse' => $moyenneClasseExamen,
            ];
        }

        $matieres = [];
        $moyennesMatieres = [];

        foreach ($parMatiere as $nomMatiere => $notes) {
            $moyenne = round(array_sum(array_column($notes, 'valeur')) / count($notes), 2);
            $moyennesMatieres[] = $moyenne;

            $matieres[] = [
                'matiere' => $nomMatiere,
                'notes' => $notes,
                'moyenne' => $moyenne,
            ];
        }

        $moyenneGenerale = count($moyennesMatieres) > 0
            ? round(array_sum($moyennesMatieres) / count($moyennesMatieres), 2)
            : null;

        return $this->json([
            'matieres' => $matieres,
            'moyenneGenerale' => $moyenneGenerale,
        ]);
    }
}
