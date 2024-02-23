<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class FileController extends AbstractController
{
    #[Route('/createjson', name: 'app_generate_json')]
    public function index(): Response
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        $array = [];

        for ($i = 0; $i < 10; $i++) {
            $element = [
                "nom" => "Hooks",
                "prenom" => "Angular",
                "email" => "angularhooks" . $i . "@key.com",
                "site" => "4",
            ];
            $array[] = $element;
        }

        $jsonContent = $serializer->serialize($array, 'json');

        // Define the path to save the JSON file
        $directory = $this->getParameter('kernel.project_dir') . '/public/json/';
        $fileName = 'data' . '.json';
        $filePath = $directory . $fileName;

        // Save the JSON data to the file
        file_put_contents($filePath, $jsonContent);

        return $this->render('file/index.html.twig', [
            'controller_name' => 'FileController',
            'content' => [1,2]
        ]);
    }

    #[Route('/readjson', name: 'app_read_json')]
    public function read(UserPasswordHasherInterface $userPasswordHasher, SiteRepository $siteRepository, EntityManagerInterface $em): Response
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $extractor = new PropertyInfoExtractor([], [new PhpDocExtractor(), new ReflectionExtractor()]);
        $normalizers = [new ObjectNormalizer(), new ArrayDenormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        // Define the path to the JSON file
        $filePath = $this->getParameter('kernel.project_dir') . '/public/json/data.json';
        $json = file_get_contents($filePath);
        $content = json_decode($json, true);

        $array = [];

        foreach ($content as $value) {
            $first = new Participant();
            $first->setActif(true);
            $first->setPassword(
                $userPasswordHasher->hashPassword(
                    $first,
                    'password'
                )
            );
            $first->setRoles(['ROLE_USER']);
            $first->setAdministrateur(false);

            $first->setNom($value['nom']);
            $first->setPrenom($value['prenom']);
            $first->setEmail($value['email']);
            $first->setSite($siteRepository->find($value['site']));

            $em->persist($first);
            $array[] = $first;
        }

        try {
            $em->flush();
        } catch (Exception $e) {
            $array[] = 'Error !';
        }

        dd($array);

        return $this->render('file/index.html.twig', [
            'controller_name' => 'FileController',
            'content' => $array
        ]);
    }



}
