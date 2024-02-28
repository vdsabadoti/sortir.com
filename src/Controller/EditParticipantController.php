<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\EditParticipantType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class EditParticipantController extends AbstractController
{
    #[Route('/edit/participant/{id}', name: 'app_edit_participant')]
    public function edit(Request $request, Participant $participant, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {

        $form = $this->createForm( EditParticipantType::class, $participant);
        $form->handleRequest($request);
        $dir = $this->getParameter('poster_dir');

        if ($form->isSubmitted() && $form->isValid()) {

            if ($form->get('poster_file')->getData() instanceof UploadedFile) {
                $pictureFile = $form->get('poster_file')->getData();
                $fileName = $slugger->slug($participant->getNom()). '-' . uniqid() . '.' . $pictureFile->guessExtension();
                $pictureFile->move($dir, $fileName);

                if ($participant->getImage() && \file_exists($dir . '/' . $participant->getImage())) {
                    unlink($dir . '/' . $participant->getImage());
                }

                $participant->setImage($fileName);
            }

            if (!empty($form->get('deleteImage')) && $form->get('deleteImage')->getData() && \file_exists($dir . '/' . $participant->getImage())) {
//                dd('ok');
                unlink($dir . '/' . $participant->getImage());
                $participant->setImage(null);
            }

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
