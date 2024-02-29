<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\RegistrationByExcelType;
use App\Form\RegistrationFormType;
use App\Services\Censurator;
use App\Services\Sender;
use App\Services\RegistrationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, Sender $sender, SluggerInterface $slugger, Censurator $censurator): Response
    {
        $user = new Participant();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

//        dd($user);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setNom($censurator->purify($user->getNom()));
            $user->setPrenom($censurator->purify($user->getPrenom()));

            $user->setActif(false);
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            if ($form->get('poster_file')->getData() instanceof UploadedFile) {
                $pictureFile = $form->get('poster_file')->getData();
                $fileName = $slugger->slug($user->getNom()) . '-' . uniqid() . '.' . $pictureFile->guessExtension();
                $pictureFile->move($this->getParameter('poster_dir'), $fileName);
                $user->setImage($fileName);
            }

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

//            $sender->sendEmail('New User', 'A new user : ' . $user->getEmail() . ' register ', 'no-reply@havingFun.com');


            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/registerByExcel', name: 'app_register_by_excel')]
    #[IsGranted('ROLE_ADMIN')]
    public function registerByExcel(Request $request, RegistrationService $registrationService,EntityManagerInterface $entityManager) : Response
    {

        $form = $this->createForm(RegistrationByExcelType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {

            if ($form->get('fichierExcel')->getData() instanceof UploadedFile) {

                try {

                    $fichierExcel = $form->get('fichierExcel')->getData();

                    //dd($fichierExcel);
                    
                    $users = $registrationService->readUserFromExcel($fichierExcel);

                    foreach ($users as $user) {
                        $entityManager->persist($user);
                    }

                    $entityManager->flush();

                    $this->addFlash('success', 'Création des utilisateurs réussie');
                }
                catch (\Exception $e)
                {

                    $this->addFlash('error', 'Création des utilisateurs échouee');
                }



            }
        }

        return $this->render('registration/register-by-excel.html.twig', [
                'form' => $form
        ]);
    }

}
