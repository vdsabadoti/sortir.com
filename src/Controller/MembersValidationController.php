<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Routing\Attribute\Route;

class MembersValidationController extends AbstractController
{
    #[Route('/members/validation', name: 'app_members_validation')]
    #[IsGranted('ROLE_ADMIN')]
    public function notification(NotifierInterface $notifier): Response
    {

        $notification = (new Notification('New User', ['email']))
            ->content('Un nouvel utilisateur souhaite rejoindre la plateforme.');

        return $this->render('members_validation/index.html.twig', [
            'controller_name' => 'MembersValidationController',
        ]);
    }
}
