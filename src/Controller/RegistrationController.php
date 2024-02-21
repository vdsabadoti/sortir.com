<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\RegistrationByExcelType;
use App\Form\RegistrationFormType;
use App\Services\RegistrationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new Participant();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

//        dd($user);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setActif(false);
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/registerByExcel', name: 'app_register_by_excel')]
    #[IsGranted('ROLE_CONTRIB')]
    public function registerByExcel(Request $request, RegistrationService $registrationService,EntityManagerInterface $entityManager) : Response
    {

        $form = $this->createForm(RegistrationByExcelType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            if ($form->get('fichierExcel')->getData() instanceof UploadedFile) {

                $fichierExcel = $form->get('fichierExcel')->getData();

                $users = $registrationService->readUserFromExcel($fichierExcel);

                foreach ($users as $user) {
                    $entityManager->persist($user);
                }

                $entityManager->flush();

            }
        }




        return $this->render('registration/register-by-excel.html.twig', [
                'form' => $form
        ]);
    }

}
