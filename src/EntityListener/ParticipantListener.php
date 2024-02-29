<?php

namespace App\EntityListener;

use App\Entity\Participant;
use App\Repository\ParticipantRepository;
use PhpOffice\PhpSpreadsheet\Shared\File;

class ParticipantListener
{

    public function __construct(private string $avatar)
    {
    }

    public function postLoad(Participant $participant): void
    {

        if (!$participant->getImage()) {
            $participant->setImage($this->avatar);
        }


    }
}