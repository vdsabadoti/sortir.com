<?php

namespace App\Controller;

use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SortieController extends AbstractController
{
    #[Route('/sorties', name: 'app_sorties')]
    public function index(SortieRepository $sortieRepository): Response
    {
        $sorties = $sortieRepository->findAll();

        return $this->render('sortieList.html.twig', [
            'sorties' => $sorties,
        ]);
    }
    #[Route('/sorties/{site}', name: 'app_sorties_par_sites',defaults: ['site'=> 'Rennes'])]
    public function sortiesBySite(?string $nomSite ,SortieRepository $sortieRepository,SiteRepository $siteRepository): Response
    {
        $sorties = $sortieRepository->findSortiesBySite($siteRepository->findSiteByNom($nomSite));

        return $this->render('sortieList.html.twig', [
            'sorties' => $sorties,
        ]);
    }
}
