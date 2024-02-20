<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class SortieController extends AbstractController
{

    #[Route('/sortie/creer', name:'app_sortie_create')]
    public function creerSortie(EntityManagerInterface $em, Request $request, ParticipantRepository $p, EtatRepository $e, SiteRepository $s) : Response
    {

        $sortie = new Sortie();

        $form = $this->createForm(SortieType::class, $sortie);

        $form->handleRequest($request);



        if($form->isSubmitted() && $form->isValid())
        {

            $em->beginTransaction();
            try {
                $sortie->setOrganisateur($p->find(1));

               // $sortie->setOrganisateur($this->getUser()->getId()) ;
                $sortie->setEtat($e->find(1));
                $sortie->setSite($s->find(1));

                // On persiste le lieu
                $em->persist($sortie->getLieu());
                $em->flush();

                // On persiste la sortie
                $em->persist($sortie);
                $em->flush();

                $em->commit();
            } catch (\Exception $e) {
                $em->rollBack();
                return $this->render('sortie/creer.html.twig', [
                    'form' => $form
                ]);
            }



            return $this->redirectToRoute('app_sorties_par_sites', ['site' => $this->getUser()->getSite()->getNom()]);
        }


        return $this->render('sortie/creer.html.twig', [
            'form' => $form
        ]);

    }

}
