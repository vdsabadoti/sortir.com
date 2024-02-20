<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class SortieController extends AbstractController
{

    #[Route('/sortie/creer', name:'app_sortie_create')]
    public function creerSortie(EntityManagerInterface $em, Request $request) : Response
    {

        $sortie = new Sortie();

        $form = $this->createForm(SortieType::class, $sortie);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $em->persist($sortie->getLieu());
            $em->flush();
            $em->persist($sortie);
            $em->flush();

            return $this->redirectToRoute('app_home');
        }


        return $this->render('sortie/creer.html.twig', [
            'form' => $form
        ]);

    }

}
