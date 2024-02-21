<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
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
    public function inscribe(Sortie $sortie, EntityManagerInterface $em, ParticipantRepository $participantRepository, EtatRepository $etatRepository): Response
    {
        //TODO doublons
        //Ajout du participant à la sortie si elle est ouverte (2)
        if ($sortie->getEtat()->getId() === 2){
            $sortie->addParticipant($participantRepository->findOneBy(['email' => $this->getUser()->getUserIdentifier()]));
            //MAJ de l'état de la sortie si nb de participants atteint (ouverte (2) => cloturé(3))
            if (count($sortie->getParticipants()) == $sortie->getNbInscriptionsMax()){
                $sortie->setEtat($etatRepository->find(3));
            }
            //UPDATE DB
            $em->persist($sortie);
            $em->flush();
            $this->addFlash('success', 'Inscription réalisé');
        } else {
            $this->addFlash('warning', 'Inscription impossible');
        }
        return $this->redirectToRoute('app_home');
    }

    #[Route('/desisted/{id}', name: 'app_desisted', requirements: ['id' => '\d+'], defaults: ['id' => 0])]
    public function desisted(Sortie $sortie, EntityManagerInterface $em, ParticipantRepository $participantRepository, EtatRepository $etatRepository): Response
    {

        //Deistement du participant à la sortie si elle est ouverte (2) ou cloturée (3)
        if ($sortie->getEtat()->getId() === 2 || $sortie->getEtat()->getId() === 3){
            $sortie->removeParticipant($participantRepository->find($this->getUser()->getUserIdentifier()));
            //MAJ de l'état de la sortie
            $sortie->setEtat($etatRepository->find(2));
            //UPDATE DB
            $em->persist($sortie);
            $em->flush();
            $this->addFlash('success', 'Desistement pris en compte');
        } else {
            $this->addFlash('warning', 'Desistement impossible');
        }
        return $this->redirectToRoute('app_home');
    }

}
