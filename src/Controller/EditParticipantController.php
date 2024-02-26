<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\EditParticipantType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EditParticipantController extends AbstractController
{
    #[Route('/edit/participant/{id}', name: 'app_edit_participant')]
    public function edit(Request $request, Participant $participant, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm( EditParticipantType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($participant);
            $entityManager->flush();

            return $this->redirectToRoute('app_profile_detail',[], Response::HTTP_SEE_OTHER);
        }

        return $this->render('edit_participant/index.html.twig', [
            'user' => $participant,
            'form' => $form,
        ]);
    }
}
