<?php

namespace App\Services;

use App\Entity\Lieu;
use App\Repository\EtatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class LieuService
{

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly HttpClientInterface $httpClient)
    {

    }

    public function suppressionPossible() : bool {

        return true;
    }

    public function definirCoordonnees( Lieu $lieu) : Lieu
    {
        $query = str_replace(' ', '+', $lieu->getRue());

        $result = $this->httpClient->request('GET', 'https://api-adresse.data.gouv.fr/search/?q='.$query.'&limit=1'/*, [
            'query' => [
                'q' => 'uen adresse en toutes lettres',
            ],
        ]*/);

        $decodedData = json_decode($result->getContent());

        $coordinates = $decodedData->features[0]->geometry->coordinates;

        $lieu->setLatitude($coordinates[0]) ; // Latitude
        $lieu->setLongitude($coordinates[1]) ; // Longitude

        return new $lieu;
    }

}