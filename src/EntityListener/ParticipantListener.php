<?php

namespace App\EntityListener;

use App\Entity\Participant;
use App\Repository\ParticipantRepository;
use PhpOffice\PhpSpreadsheet\Shared\File;

class ParticipantListener
{

    public function __construct()
    {
    }

    public function postLoad(Participant $participant): void
    {

        $defaultAvatar = "defaultAvatar.svg";

        if (!$participant->getImage()) {
            $participant->setImage($defaultAvatar);
        }


    }
}