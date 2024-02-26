<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\PasswordUpdateFromProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class UpdatePasswordFromProfileController extends AbstractController
{
    #[Route('/update/password/from/profile/{id}', name: 'app_update_password_from_profile')]
    public function index(Request $request, Participant $participant, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {

        $form = $this->createForm(PasswordUpdateFromProfileType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $participant->setPassword(
                $userPasswordHasher->hashPassword(
                    $participant,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($participant);
            $entityManager->flush();

            return $this->redirectToRoute('app_profile_detail', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('update_password_from_profile/index.html.twig', [
            'user' => $participant,
            'form' => $form,
        ]);
    }
}
