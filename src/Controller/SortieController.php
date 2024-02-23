<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use App\Services\SortiesService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class SortieController extends AbstractController
{

    #[Route('/sortie/create', name:'app_sortie_create')]
    public function create(EntityManagerInterface $em, Request $request, EtatRepository $e) : Response
    {

        $sortie = new Sortie();
        $user = $this->getUser();

        $form = $this->createForm(SortieType::class, $sortie, [
            'addLieu' => isset($request->get('sortie')['AjouterLieu']),
            'InsererLieu' => isset($request->get('sortie')['InsererLieu'])
        ]);

        $sortie->setOrganisateur($user);

        $form->handleRequest($request);

        //dd($form);

        if(isset($request->get('sortie')['AjouterLieu']))
        {
            return $this->render('sortie/creer.html.twig', [
                'form' => $form
            ]);
        }

        if($form->isSubmitted() && $form->isValid())
        {

            $em->beginTransaction();
            try {

                $sortie->setEtat($e->find(1));
                $sortie->setSite($sortie->getOrganisateur()->getSite());

                // On persiste le lieu
                $em->persist($sortie->getLieu());
                $em->flush();

                // On persiste la sortie
                $em->persist($sortie);
                $em->flush();

                $em->commit();
            } catch (\Exception $e) {
                $em->rollBack();
                //var_dump($sortie);
                var_dump($e->getMessage());
                //dd($e->getMessage());
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

    #[Route('/sortie/update/{id}', name:'app_sortie_update', requirements: ['id' => '\d+'])]
    public function update(EntityManagerInterface $em, Request $request, Sortie $sortie) : Response
    {

        /* Si l'utilisateur veut ajouter un lieu on lui mets le formulaire de creation de lieu
           tout en supprimant de l'objet transféré le lieu selectionner afin que les champs du nouveau formulaire soient vides
        */
        if(isset($request->get('sortie')['AjouterLieu']))
        {

            $form = $this->createForm(SortieType::class, $sortie, [
                'addLieu' => isset($request->get('sortie')['AjouterLieu']),
                'InsererLieu' => isset($request->get('sortie')['InsererLieu'])
            ]);

            $form->get('lieu')->setData(null);

            $form->handleRequest($request);

            return $this->render('sortie/creer.html.twig', [
                'form' => $form
            ]);
        }


        $form = $this->createForm(SortieType::class, $sortie, [
            'addLieu' => isset($request->get('sortie')['AjouterLieu']),
            'InsererLieu' => isset($request->get('sortie')['InsererLieu'])
        ]);

        // On resupprime le lieu de la liste déroulante afin de laisser place au nouveau afin de faire une insertion
        if(isset($request->get('sortie')['InsererLieu']))
        {
            $form->get('lieu')->setData(null);
        }

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $em->beginTransaction();
            try {

                //dd($sortie->getLieu());
                // On persiste le lieu
                $em->persist($sortie->getLieu());
                $em->flush();

                // On persiste la sortie
                $em->persist($sortie);
                $em->flush();

                $em->commit();
            } catch (\Exception $e) {
                $em->rollBack();
                var_dump($e->getMessage());
                return $this->render('sortie/update.html.twig', [
                    'form' => $form
                ]);
            }

            return $this->redirectToRoute('app_sorties_par_sites', ['site' => $this->getUser()->getSite()->getNom()]);
        }


        return $this->render('sortie/update.html.twig', [
            'form' => $form
        ]);


    }

    #[Route('/sortie/cancel/{id}', name: 'app_sortie_cancel', requirements: ['id' => '\d+'])]
    public function cancelSortie(EntityManagerInterface $em, Sortie $sortie, EtatRepository $etat, SortiesService $sortiesService) : Response
    {

        if($sortiesService->verifAnnulerSortie($sortie))
        {
            //dd($sortie);
            $sortie->setEtat($etat->find(6));

            $em->persist($sortie);
            $em->flush();

            return $this->redirectToRoute('app_sorties_par_sites', ['site' => $this->getUser()->getSite()->getNom()]);
        }

        $this->addFlash('error', 'Impossible d\'annuler une sortie qui n\'est pas encore ouverte ou qui a deja débuté');
        return $this->render('sortie/detail.html.twig');

    }

    #[Route('/sortie/delete/{id}', name: 'app_sorties_delete', requirements: ['id' => '\d+'])]
    public function deleteSortie(EntityManagerInterface $em, Sortie $sortie, EtatRepository $etat, SortiesService $sortiesService) : Response
    {

        if($sortiesService->verifSuppressionSortie($sortie))
        {

            //dd("test");

            $em->beginTransaction();
            try {

                $em->remove($sortie);
                $em->flush();

                $em->commit();
            } catch (\Exception $e) {
                $em->rollBack();
                var_dump($e->getMessage());


            }
            return $this->redirectToRoute('app_sorties_par_sites', ['site' => $this->getUser()->getSite()->getNom()]);
        }

        $this->addFlash('error', 'Impossible de supprimer une sortie publiée');
        return $this->redirectToRoute('app_sorties_par_sites', ['site' => $this->getUser()->getSite()->getNom()]);



    }

}
