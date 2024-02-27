<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\RegistrationFormType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UsersListController extends AbstractController
{
    #[Route('/users/list', name: 'app_users_list')]
    #[IsGranted('ROLE_ADMIN')]
    public function user(ParticipantRepository $participantRepository): Response
    {
        return $this->render('users_list/index.html.twig', [
            'users' => $participantRepository->findAll(),
        ]);
    }

//    #[Route('/edit/{id}', name: 'app_user_edit', methods: ['GET', 'POST'])]
//    public function edit(Request $request, Participant $participant, EntityManagerInterface $entityManager): Response
//    {
//        $form = $this->createForm(RegistrationFormType::class, $participant);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $entityManager->persist($participant);
//            $entityManager->flush();
//
//            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
//        }
//
//        return $this->render('users_list/edit.html.twig', [
//            'user' => $participant,
//            'form' => $form,
//        ]);

//    }


    #[Route('/delete/{id}', name: 'app_user_delete')]
    public function delete(Participant $participant, EntityManagerInterface $entityManager): Response
    {


        $entityManager->remove($participant);
        $entityManager->flush();


        return $this->redirectToRoute('app_users_list', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/activate/{id}', name: 'app_user_activate')]
    public function Activate(Participant $participant, EntityManagerInterface $entityManager): Response
    {


        $participant->setActif(true);
        $entityManager->flush();


        return $this->redirectToRoute('app_users_list', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/desactivate/{id}', name: 'app_user_desactivate')]
    public function Desactivate(Participant $participant, EntityManagerInterface $entityManager): Response
    {


        $participant->setActif(false);
        $entityManager->flush();


        return $this->redirectToRoute('app_users_list', [], Response::HTTP_SEE_OTHER);
    }

}
