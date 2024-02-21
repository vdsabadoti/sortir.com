<?php

namespace App\Controller;

use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SortieController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(SortieRepository $sortieRepository): Response
    {
        $sorties = $sortieRepository->findAll();

        return $this->render('sortie/sortieList.html.twig', [
            'sorties' => $sorties,
        ]);
    }
    #[Route('/{site}', name: 'app_sorties_par_sites',defaults: ['site'=> 'Rennes'])]
    public function sortiesBySite(?string $site ,SortieRepository $sortieRepository,SiteRepository $siteRepository): Response
    {
        $sorties = $sortieRepository->findBy([ 'site' => $siteRepository->findOneBy(['nom' => $site])]);

        return $this->render('sortie/sortieList.html.twig', [
            'sorties' => $sorties,
        ]);
    }
}
