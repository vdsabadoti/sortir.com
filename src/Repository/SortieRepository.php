<?php

namespace App\Repository;

use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard\DateTime;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function findFilteredByState(bool $isAdmin, $organisateur): array
    {
        $q = $this->createQueryBuilder('s')
            ->where('s.etat = 2 OR s.etat = 3 OR s.etat = 4 OR s.etat = 5 OR s.etat = 6');

        if($isAdmin) {
                $q->orWhere('s.etat = 1');
            } else {
            $q->orWhere('s.etat = 1 AND s.organisateur = :organisateur')
                ->setParameter('organisateur', $organisateur);
        }
            return $q->getQuery()->getResult();
    }

    public function findFilteredBySite(bool $isAdmin, Participant $organisateur, Site $site): array
    {
        $q = $this->createQueryBuilder('s')
            ->where('s.site = :site')
            ->setParameter('site', $site)
            ->andWhere('s.etat = 2 OR s.etat = 3 OR s.etat = 4 OR s.etat = 5 OR s.etat = 6');

        if($isAdmin) {
            $q->orWhere('s.etat = 1');
        } else {
            $q->orWhere('s.etat = 1 AND s.organisateur = :organisateur')
                ->setParameter('organisateur', $organisateur);
        }

        return $q->getQuery()->getResult();
    }

    public function search(string $search, $organisateur, bool $isAdmin): array
    {
        $q = $this->createQueryBuilder('s')
            ->where('s.nom LIKE :search')
            ->setParameter('search',"%".$search."%")
            ->andWhere('s.etat = 2 OR s.etat = 3 OR s.etat = 4 OR s.etat = 5 OR s.etat = 6');

        if ($isAdmin) {
            $q->orWhere('s.etat = 1');
        } else {
            $q->orWhere('s.etat = 1 AND s.organisateur = :organisateur')
                ->setParameter('organisateur', $organisateur);
        }
            return $q->getQuery()
                ->getResult();
        ;
    }

    public function searchdate(string $start,string $end, $organisateur, bool $isAdmin): array
    {
        $end = \DateTime::createFromFormat("m/d/Y", $end);
        $start = \DateTime::createFromFormat("m/d/Y", $start);

        $q = $this->createQueryBuilder('s')
            ->andWhere('s.dateHeureDebut <= :end')
            ->andWhere('s.dateHeureDebut >= :start')
            ->setParameter('end',$end)
            ->setParameter('start',$start)
            ->andWhere('s.etat = 2 OR s.etat = 3 OR s.etat = 4 OR s.etat = 5 OR s.etat = 6');

        if ($isAdmin) {
            $q->orWhere('s.etat = 1');
        } else {
            $q->orWhere('s.etat = 1 AND s.organisateur = :organisateur')
                ->setParameter('organisateur', $organisateur);
        }
        return $q->getQuery()
            ->getResult();

    }





    public function findParticipe(Participant $participant):array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.etat = 1 OR s.etat = 2 OR s.etat = 3 OR s.etat = 4 OR s.etat = 5 OR s.etat = 6')
            ->join('s.participants', 'p', 'WITH', 'p = :participant')
            ->setParameter('participant', $participant)
            ->getQuery()
            ->getResult();
    }




//    /**
//     * @return Sortie[] Returns an array of Sortie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Sortie
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
