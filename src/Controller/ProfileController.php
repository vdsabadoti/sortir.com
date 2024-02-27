<?php

namespace App\Controller;

use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/profile', name: 'app_profile')]
#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
    #[Route('/detail', name: '_detail')]
    public function profilePage(): Response
    {

        return $this->render('users_list/user_profile.html.twig', [

        ]);
    }
    #[Route('/detail/{id}', name: '_detail_id')]
    public function profilePageid(?int $id, ParticipantRepository $participantRepository): Response
    {
        $user = $participantRepository->find($id);


        return $this->render('users_list/detail_profile.html.twig', [
            'user' => $user
        ]);
    }

}
