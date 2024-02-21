<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class InscriptionController extends AbstractController
{
    #[Route('/inscribe/{id}', name: 'app_inscription', requirements: ['id' => '\d+'], defaults: ['id' => 0])]
    public function inscribe(Sortie $sortie, EntityManagerInterface $em, ParticipantRepository $participantRepository, EtatRepository $etatRepository): Response
    {
        //Ajout du participant à la sortie si elle est ouverte (2)
        if ($sortie->getEtat()->getId() === 2){
            $sortie->addParticipant($participantRepository->find($this->getUser()->getUserIdentifier()));
            //MAJ de l'état de la sortie si nb de participants atteint (ouverte (2) => cloturé(3))
            if ($sortie->getParticipants() == $sortie->getNbInscriptionsMax()){
                $sortie->setEtat($etatRepository->find(3));
            }
            //UPDATE DB
            $em->persist($sortie);
            $em->flush();
            //TODO message inscription OK
        } else {
            //TODO message inscription impossible
        }

        return $this->redirectToRoute('app_home');
    }

    #[Route('/desisted/{id}', name: 'app_inscription', requirements: ['id' => '\d+'], defaults: ['id' => 0])]
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
            //TODO message inscription OK
        } else {
            //TODO message desistement impossible
        }

        return $this->redirectToRoute('app_home');
    }

}
