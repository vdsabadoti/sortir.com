<?php

namespace App\Controller;

use App\Form\RegistrationFormType;
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
        $sorties = $sortieRepository->findAll();
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
    #[Route('/inscrit/{id}', name: 'app_sorties_inscrit')]
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


}
