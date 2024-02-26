<?php

namespace App\EntityListener;

use App\Entity\Sortie;
use App\Repository\EtatRepository;
use Doctrine\ORM\EntityManagerInterface;
use function Symfony\Component\Clock\now;

class SortieListener
{
    public function __construct(
        private readonly EtatRepository $etatRepository,
        private readonly EntityManagerInterface $em){

    }

    //Mise à jour des sorties (passé et archivé)
    public function postLoad(Sortie $sortie){

        $currentDateTime = new \DateTime();
        $delayArchive = 30;

        $debutSortie = $sortie->getDateHeureDebut();
        $duree = $sortie->getDuree();
        $finSortie = clone $debutSortie;
        $finSortie->modify("+ $duree minutes");


        if (in_array($sortie->getEtat()->getId(), [2,3,4])) {

            ///La date limite d'inscription est passé ?
            if ($sortie->getDateLimiteInscription() < $currentDateTime) {
                $nouveauEtat = 3; //CLOTURE

                ///Le début de la sortie est dans le passé ?
                if ($sortie->getDateHeureDebut() < $currentDateTime) {
                    $nouveauEtat = 5; //PASSE

                    //La sortie n'est pas encore finie ?
                    if ($finSortie > $currentDateTime) {
                        $nouveauEtat = 4; //ACTIVITE EN COURS
                    }

                    //La sortie doit-elle être archivée ?
                    if ($sortie->getDateHeureDebut()->modify("+$delayArchive days") < $currentDateTime) {
                        $nouveauEtat = 7; //ARCHIVEE
                    }
                }
                //Mise à jour de la sortie
                $this->miseAJourEtat($sortie, $nouveauEtat);
            }
        }
    }

    private function miseAJourEtat(Sortie $sortie, $id) : void {
        $etat = $this->etatRepository->find($id);
        $sortie->setEtat($etat);
        $this->em->persist($sortie);
        $this->em->flush();
    }

}