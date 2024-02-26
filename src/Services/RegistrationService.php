<?php

namespace App\Services;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints\Date;

class RegistrationService
{
    public function __construct(
        private readonly SiteRepository $siteRepository,
        private readonly UserPasswordHasherInterface $userPasswordHasher)
    {

    }


    public function readUserFromExcel($file): array
    {
        $reader = IOFactory::createReader('Xlsx');

        $spreadsheet = $reader->load($file->getPathname());

        $worksheet = $spreadsheet->getActiveSheet();

        $highestRow = $worksheet->getHighestRow();

        $users = [];

        for ($row = 2; $row <= $highestRow; ++$row) {

            $user = new Participant();

            $user->setNom($worksheet->getCell([1, $row])->getValue());
            $user->setPrenom($worksheet->getCell([2, $row])->getValue());
            $user->setTelephone($worksheet->getCell([3, $row])->getValue());
            $user->setPassword($this->userPasswordHasher->hashPassword( $user,$worksheet->getCell( [4, $row] )->getValue() )  );
            $user->setAdministrateur($worksheet->getCell([5, $row])->getValue());
            $user->setActif($worksheet->getCell([6, $row])->getValue());
            $user->setEmail($worksheet->getCell([7, $row])->getValue());
            $user->setRoles(array());
            $user->setSite( $this->siteRepository->findOneBy( ['nom' => strtoupper( $worksheet->getCell([9, $row])->getValue() )] ) );

            $users[] = $user;

        }

        return $users;
    }

}