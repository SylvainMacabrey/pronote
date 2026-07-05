<?php

namespace App\Controller;

use App\Entity\Eleve;
use App\Entity\Professeur;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class ProfilController extends AbstractController
{
    #[Route('/api/profil', name: 'app_profil', methods: ['GET'])]
    public function index(Security $security): JsonResponse
    {
        $user = $security->getUser();

        $role = match (true) {
            $user instanceof Eleve => 'ELEVE',
            $user instanceof Professeur => 'PROFESSEUR',
            default => null,
        };

        return $this->json([
            'nom' => $user->getNom(),
            'prenom' => $user->getPrenom(),
            'role' => $role,
        ]);
    }
}
