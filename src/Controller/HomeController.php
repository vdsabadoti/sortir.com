<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class HomeController extends AbstractController
{
    #[Route('/login', name: 'app_home')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render('security/login.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/about/us', name: 'app_about_us')]
    public function credit(): Response
    {
        return $this->render('home/aboutUs.html.twig', [
        ]);
    }

}
