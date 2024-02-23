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


        if (in_array($sortie->getEtat()->getId(), [2,3,4])){

            ///Le début de la sortie est dans le passé ?
            if ($sortie->getDateHeureDebut() < $currentDateTime){

                //Si oui =>
                $nouveauEtat = 5;

                //La sortie n'est pas encore finie ?
                if ($finSortie > $currentDateTime) {
                    //Passage à l'état activité en cours (4)
                    $nouveauEtat = 4;
                }

                //La sortie doit-elle être archivée ?
                if ($sortie->getDateHeureDebut()->modify("+$delayArchive days") < $currentDateTime) {
                    //Passage à l'état archivé (7)
                    $nouveauEtat = 7;
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