<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use App\Services\LieuService;
use App\Services\SortiesService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


class SortieController extends AbstractController
{

    #[Route('/sortie/create', name:'app_sortie_create')]
    #[IsGranted('ROLE_USER')]
    public function create(EntityManagerInterface $em, Request $request, EtatRepository $e, LieuService $lieuService) : Response
    {

        $sortie = new Sortie();
        $user = $this->getUser();

        $form = $this->createForm(SortieType::class, $sortie, [
            'addLieu' => isset($request->get('sortie')['AjouterLieu']),
            'InsererLieu' => isset($request->get('sortie')['InsererLieu'])
        ]);

        $sortie->setOrganisateur($user);

        $form->handleRequest($request);


        if(isset($request->get('sortie')['AjouterLieu']) || isset($request->get('sortie')['SelectionnerLieuxDisponibles']) )
        {

            $form->clearErrors();

            return $this->render('sortie/creer.html.twig', [
                'form' => $form
            ]);
        }

        if($form->isSubmitted() && $form->isValid())
        {
            $em->beginTransaction();
            try {

                //On définit l'état (en création ou ouvert)
                $sortie->setEtat($e->find(1));
                if (array_key_exists('publier', $request->get('sortie'))){
                    if ($request->get('sortie')['publier']){
                        $sortie->setEtat($e->find(2));
                    }
                }

                $sortie->setSite($sortie->getOrganisateur()->getSite());

                // On persiste le lieu
                $sortie->getLieu()->setActif(true); // On dit que le lieu est actif
                $lieuService->definirCoordonnees($sortie->getLieu());
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
    #[IsGranted('ROLE_USER')]
    public function update(EntityManagerInterface $em, Request $request, Sortie $sortie, SortiesService $sortiesService, LieuService $lieuService, EtatRepository $e) : Response
    {

        if($sortiesService->verifModificationSortie($sortie, $this->getUser()) )
        {
            /* Si l'utilisateur veut ajouter un lieu on lui mets le formulaire de creation de lieu
                       tout en supprimant de l'objet transféré le lieu selectionner afin que les champs du nouveau formulaire soient vides
                    */
            if(isset($request->get('sortie')['AjouterLieu']) || isset($request->get('sortie')['SelectionnerLieuxDisponibles']))
            {

                $form = $this->createForm(SortieType::class, $sortie, [
                    'addLieu' => isset($request->get('sortie')['AjouterLieu']),
                    'InsererLieu' => isset($request->get('sortie')['InsererLieu'])
                ]);

                $form->get('lieu')->setData(null);

                $form->handleRequest($request);

                $form->clearErrors();

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

                    //On définit l'état (en création ou ouvert)
                    $sortie->setEtat($e->find(1));
                    if (array_key_exists('publier', $request->get('sortie'))){
                        if ($request->get('sortie')['publier']){
                            $sortie->setEtat($e->find(2));
                        }
                    }

                    // On persiste le lieu
                    $sortie->getLieu()->setActif(true); // On dit que le lieu est actif
                    $lieuService->definirCoordonnees($sortie->getLieu());
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

                $this->addFlash('success', 'Modification effectuée');
                return $this->redirectToRoute('app_sorties_par_sites', ['site' => $this->getUser()->getSite()->getNom()]);
            }


            return $this->render('sortie/update.html.twig', [
                'form' => $form
            ]);
        }

        $this->addFlash('error', 'Vous ne pouvez pas modifier une sortie deja publiée');
        return $this->redirectToRoute('app_home');

    }

    #[Route('/sortie/cancel/{id}', name: 'app_sortie_cancel', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_USER')]
    public function cancelSortie(EntityManagerInterface $em, Sortie $sortie, EtatRepository $etat, SortiesService $sortiesService) : Response
    {

        if($sortiesService->verifAnnulerSortie($sortie, $this->getUser()))
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
    #[IsGranted('ROLE_USER')]
    public function deleteSortie(EntityManagerInterface $em, Sortie $sortie, EtatRepository $etat, SortiesService $sortiesService) : Response
    {

        if($sortiesService->verifSuppressionSortie($sortie, $this->getUser()))
        {
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

    #[Route('/', name: 'app_home')]
    #[IsGranted('ROLE_USER')]
    public function index(SortieRepository $sortieRepository,SiteRepository $siteRepository): Response
    {

        $isAdmin = $this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN');
        $sorties = $sortieRepository->findFilteredByState($isAdmin, $this->getUser());

        $sites = $siteRepository->findAll();

        return $this->render('sortie/sortieList.html.twig', [
            'sorties' => $sorties,
            'sites' => $sites,
        ]);
    }

    #[Route('/mesSorties', name: 'app_mes_sorties')]
    #[IsGranted('ROLE_USER')]
    public function mesSorties(SortieRepository $sortieRepository,SiteRepository $siteRepository): Response
    {
        $sorties = $sortieRepository->findBy(['organisateur' => $this->getUser()]);
        $sites = $siteRepository->findAll();

        return $this->render('sortie/sortieList.html.twig', [
            'sorties' => $sorties,
            'sites' => $sites,
        ]);
    }
    #[Route('/jeParticipe', name: 'app_mes_participation')]
    #[IsGranted('ROLE_USER')]
    public function mesParticipation(SortieRepository $sortieRepository,SiteRepository $siteRepository,ParticipantRepository $participantRepository): Response
    {
        //$user = $this->getUser();
        //$participant = $participantRepository->findOneBy(['email'=>$user->getUserIdentifier()]);
        //$sorties = $participant->getSorties();


        $sorties = $sortieRepository->findParticipe($this->getUser());
        $sites = $siteRepository->findAll();

        return $this->render('sortie/sortieList.html.twig', [
            'sorties' => $sorties,
            'sites' => $sites,
        ]);
    }
    #[Route('/search', name: 'app_search',methods: 'post')]
    #[IsGranted('ROLE_USER')]
    public function search(Request $request ,SortieRepository $sortieRepository,SiteRepository $siteRepository): Response
    {
        $isAdmin = $this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN');
        $sorties = $sortieRepository->search($request->get('search'), $this->getUser(), $isAdmin);
        $sites = $siteRepository->findAll();

        return $this->render('sortie/sortieList.html.twig', [
            'sorties' => $sorties,
            'sites' => $sites,
        ]);
    }

    #[Route('/campus/{site}', name: 'app_sorties_par_sites',defaults: ['site'=> 'Nantes'])]
    #[IsGranted('ROLE_USER')]
    public function sortiesBySite(?string $site ,SortieRepository $sortieRepository,SiteRepository $siteRepository): Response
    {
        $isAdmin = $this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN');
        $sorties = $sortieRepository->findFilteredBySite($isAdmin, $this->getUser(), $siteRepository->findOneBy(['nom' => $site]));
        $sites = $siteRepository->findAll();

        return $this->render('sortie/sortieList.html.twig', [
            'sorties' => $sorties,
            'sites' => $sites,
        ]);
    }
    #[Route('/inscrit/{id}', name: 'app_sorties_inscrit')]
    #[IsGranted('ROLE_USER')]
    public function inscrire(?int $id ,SortieRepository $sortieRepository,SiteRepository $siteRepository,EntityManagerInterface $entityManager): Response
    {
        $sortie = $sortieRepository->find($id);
        $sortie->addParticipant($this->getUser());
        $entityManager->persist($sortie);
        $entityManager->flush();

        $sorties = $sortieRepository->findAll();
        $sites = $siteRepository->findAll();

        return $this->render('sortie/sortieList.html.twig', [
            'sorties' => $sorties,
            'sites' => $sites,
        ]);
    }
    #[Route('/desinscrit/{id}', name: 'app_sorties_desinscrit')]
    #[IsGranted('ROLE_USER')]
    public function desinscrire(?int $id ,SortieRepository $sortieRepository,SiteRepository $siteRepository,EntityManagerInterface $entityManager): Response
    {
        $sortie = $sortieRepository->find($id);
        $sortie->removeParticipant($this->getUser());
        $entityManager->persist($sortie);
        $entityManager->flush();

        $sorties = $sortieRepository->findAll();
        $sites = $siteRepository->findAll();

        return $this->render('sortie/sortieList.html.twig', [
            'sorties' => $sorties,
            'sites' => $sites,
        ]);
    }
    #[Route('/detail/{id}', name: 'app_sorties_detail')]
    #[IsGranted('ROLE_USER')]
    public function detail(?int $id ,SortieRepository $sortieRepository,SiteRepository $siteRepository,EntityManagerInterface $entityManager): Response
    {
        $sortie = $sortieRepository->find($id);

        $sites = $siteRepository->findAll();

        return $this->render('sortie/detail.html.twig', [
            'sortie' => $sortie,
            'sites' => $sites,
        ]);
    }

    #[Route('/annuler/{id}', name: 'app_sorties_annuler')]
    public function annuler(?int $id ,SortieRepository $sortieRepository,SiteRepository $siteRepository, EtatRepository $etatRepository,EntityManagerInterface $entityManager): Response
    {
        $sortie = $sortieRepository->find($id);
        //Annulation possible si la sortie est CREE, OUVERTE ou CLOTURE
        if($sortie->getEtat()->getId() == 1 || $sortie->getEtat()->getId() == 2 || $sortie->getEtat()->getId() == 3 ) {
            $sortie->setEtat($etatRepository->find(6));
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', 'Sortie annulée');
        } else {
            $this->addFlash('warning', 'Annulation impossible');
        }
        return $this->redirectToRoute('app_home');
    }

}
