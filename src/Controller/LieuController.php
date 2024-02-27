<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/lieu')]
#[IsGranted('ROLE_ADMIN')]
class LieuController extends AbstractController
{
    #[Route('/', name: 'app_lieu_index', methods: ['GET', 'POST'])]
    public function index(Request $request, LieuRepository $lieuRepository, VilleRepository $villeRepository): Response
    {
        $villes = $villeRepository->findAll();

        $text = $request->request->get('lieu');
        $desactif = $request->request->get('desactif');
        $ville = $request->request->get('ville');

        $lieux = $lieuRepository->findByTextAndStateAndVille($text, $desactif, $ville);
        return $this->render('lieu/index.html.twig', [
            'lieux' => $lieux,
            'villes' => $villes
        ]);

    }

    #[Route('/new', name: 'app_lieu_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $lieu = new Lieu();
        $form = $this->createForm(LieuType::class, $lieu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($lieu);
            $entityManager->flush();

            return $this->redirectToRoute('app_lieu_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('lieu/new.html.twig', [
            'lieu' => $lieu,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_lieu_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Lieu $lieu, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LieuType::class, $lieu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_lieu_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('lieu/edit.html.twig', [
            'lieu' => $lieu,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_lieu_delete', methods: ['POST'])]
    public function delete(Request $request, Lieu $lieu, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$lieu->getId(), $request->request->get('_token'))) {
            if (sizeof($lieu->getSorties()) == 0){
                $entityManager->remove($lieu);
                $entityManager->flush();
                $this->addFlash('success', 'Lieu supprimé');
            } else {
                $this->addFlash('warning', 'Suppression impossible');
            }
        }
        return $this->redirectToRoute('app_lieu_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/switch', name: 'app_lieu_switch', methods: ['GET'])]
    public function switch(Lieu $lieu, EntityManagerInterface $entityManager): Response
    {
        if ($lieu->isActif()) {
            $lieu->setActif(false);
        } else {
            $lieu->setActif(true);
        }
        $entityManager->persist($lieu);
        $entityManager->flush();

        return $this->redirectToRoute('app_lieu_index', [], Response::HTTP_SEE_OTHER);
    }
}
