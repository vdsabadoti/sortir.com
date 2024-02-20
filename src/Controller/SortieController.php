<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class SortieController extends AbstractController
{

    #[Route('/sortie/creer', name:'app_sortie_create')]
    public function creerSortie(EntityManagerInterface $em, Request $request, ParticipantRepository $p, EtatRepository $e) : Response
    {

        $sortie = new Sortie();

        $form = $this->createForm(SortieType::class, $sortie);

        $form->handleRequest($request);



        if($form->isSubmitted() && $form->isValid())
        {


            $em->beginTransaction();
            try {
                //$this->getUser()->getId();
                $sortie->setOrganisateur($p->find(1));
                $sortie->setEtat($e->find(1));
                $em->persist($sortie->getLieu());
                $em->flush();
                $em->persist($sortie);
                $em->flush();

                $em->commit();
            } catch (\Exception $e) {
                $em->rollBack();
                // Gérez l'exception
            }



            return $this->redirectToRoute('app_home');
        }


        return $this->render('sortie/creer.html.twig', [
            'form' => $form
        ]);

    }

}
