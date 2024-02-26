<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\EditParticipantByAdminType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EditParticipantByAdminController extends AbstractController
{
    #[Route('/edit/participant/by/admin/{id}', name: 'app_edit_participant_by_admin')]
    public function edit(Request $request, Participant $participant, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EditParticipantByAdminType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($participant);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index',[],Response::HTTP_SEE_OTHER);
        }

        return $this->render('edit_participant_by_admin/index.html.twig', [
            'user'=> $participant,
            'form'=> $form,
        ]);
    }
}
