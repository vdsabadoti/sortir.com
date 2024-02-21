<?php

namespace App\Services;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;

class SortiesService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly EtatRepository $etatRepository,
        private readonly ParticipantRepository $participantRepository)
    {

    }

    //Ajouter un participant à la sortie (ouverte)
    public function ajouterParticipant(Sortie $sortie, string $identifiant) : bool
    {
        $participant = $this->participantRepository->findOneBy(['email' => $identifiant]);

        if ($this->sortieOuverte($sortie) && !$this->participantInscrit($sortie, $participant) && !$this->estOrganisateur($sortie, $participant)) {
            $sortie->addParticipant($participant);
            $this->majEtatSortie($sortie);
            $this->majSortieBaseDonnees($sortie);
            return true;
        }
        return false;
    }

    //Retirer un participant d'une sortie (ouverte ou cloturée)
    public function retirerParticipant(Sortie $sortie, string $identifiant) : bool
    {
        $participant = $this->participantRepository->findOneBy(['email' => $identifiant]);

        if ($this->participantInscrit($sortie, $participant) && $this->sortieOuverte($sortie) || $this->sortieCloturee($sortie)) {
            $sortie->removeParticipant($participant);
            $this->majEtatSortie($sortie);
            $this->majSortieBaseDonnees($sortie);
            return true;
        }
        return false;
    }


    //Verifie si la sortie est ouverte
    private function sortieOuverte(Sortie $sortie) : bool {
        return ($sortie->getEtat()->getId() === 2);
    }

    //Verifie si la sortie est cloturée
    private function sortieCloturee(Sortie $sortie) : bool {
        return ($sortie->getEtat()->getId() === 3);
    }


    //Verifie si un participant est inscrit dans un sortie
    private function participantInscrit(Sortie $sortie, Participant $participant) : bool {
        $participants = $sortie->getParticipants();
        foreach ($participants as $p){
            if ($p->getId() == $participant->getId()){
                return true;
            }
        }
        return false;
    }

    //Mise à jour de l'état d'une sortie
    private function majEtatSortie(Sortie $sortie) : void {

        //Cloturé si nb de participants atteint, sinon ouverte
        if (count($sortie->getParticipants()) == $sortie->getNbInscriptionsMax()) {
            $sortie->setEtat($this->etatRepository->findOneBy(['id'=> 3]));
        } else {
            $sortie->setEtat($this->etatRepository->findOneBy(['id'=> 2]));
        }
    }

    //Mise à jour de la base de données
    private function majSortieBaseDonnees(Sortie $sortie){
        $this->em->persist($sortie);
        $this->em->flush();
    }

    //Verifie si le participant est l'organisateur de la sortie
    private function estOrganisateur(Sortie $sortie, Participant $participant) : bool {
        return ($sortie->getOrganisateur()->getId() == $participant->getId());
    }

}