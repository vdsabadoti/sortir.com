<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Services\SortiesService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class InscriptionController extends AbstractController
{
    #[Route('/test', name: 'app_test')]
    public function test(): Response
    {
        return $this->redirectToRoute('app_home');
    }

    #[Route('/inscribe/{id}', name: 'app_inscribe', requirements: ['id' => '\d+'], defaults: ['id' => 1])]
    public function inscribe(Sortie $sortie, SortiesService $sortiesService): Response
    {
        if ($sortiesService->ajouterParticipant($sortie, $this->getUser()->getUserIdentifier())){
            $this->addFlash('success', 'Inscription réalisé');
        } else {
            $this->addFlash('warning', 'Inscription impossible');
        }
        return $this->redirectToRoute('app_home');
    }

    #[Route('/desisted/{id}', name: 'app_desisted', requirements: ['id' => '\d+'], defaults: ['id' => 0])]
    public function desisted(Sortie $sortie, SortiesService $sortiesService): Response
    {
        if ($sortiesService->retirerParticipant($sortie, $this->getUser()->getUserIdentifier())){
            $this->addFlash('success', 'Desistement pris en compte');
        } else {
            $this->addFlash('warning', 'Desistement impossible');
        }
        return $this->redirectToRoute('app_home');
    }

}
