<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SortieController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(SortieRepository $sortieRepository,SiteRepository $siteRepository): Response
    {
        $sorties = $sortieRepository->findFilteredByState();
        $sites = $siteRepository->findAll();

        return $this->render('sortie/sortieList.html.twig', [
            'sorties' => $sorties,
            'sites' => $sites,
        ]);
    }

    #[Route('/mesSorties', name: 'app_mes_sorties')]
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
    public function mesParticipation(SortieRepository $sortieRepository,SiteRepository $siteRepository,ParticipantRepository $participantRepository): Response
    {
        $user = $this->getUser();
        $participant = $participantRepository->findOneBy(['email'=>$user->getUserIdentifier()]);
        $sorties = $participant->getSorties();


        //$sorties = $sortieRepository->findParticipe($this->getUser());
        $sites = $siteRepository->findAll();

        return $this->render('sortie/sortieList.html.twig', [
            'sorties' => $sorties,
            'sites' => $sites,
        ]);
    }
    #[Route('/search', name: 'app_search',methods: 'post')]
    public function search(Request $request ,SortieRepository $sortieRepository,SiteRepository $siteRepository): Response
    {
        $sorties = $sortieRepository->search($request->get('search'));
        $sites = $siteRepository->findAll();

        return $this->render('sortie/sortieList.html.twig', [
            'sorties' => $sorties,
            'sites' => $sites,
        ]);
    }

    #[Route('/site/{site}', name: 'app_sorties_par_sites',defaults: ['site'=> 'Rennes'])]
    public function sortiesBySite(?string $site ,SortieRepository $sortieRepository,SiteRepository $siteRepository): Response
    {
        $sorties = $sortieRepository->findBy([ 'site' => $siteRepository->findOneBy(['nom' => $site])]);
        $sites = $siteRepository->findAll();

        return $this->render('sortie/sortieList.html.twig', [
            'sorties' => $sorties,
            'sites' => $sites,
        ]);
    }

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
