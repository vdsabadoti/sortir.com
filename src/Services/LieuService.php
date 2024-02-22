<?php

namespace App\Services;

use App\Repository\EtatRepository;
use Doctrine\ORM\EntityManagerInterface;

class LieuService
{

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly EtatRepository $etatRepository,
        private readonly SortiesService $sortiesService)
    {

    }

    public function suppressionPossible() : bool {

        return true;
    }

}