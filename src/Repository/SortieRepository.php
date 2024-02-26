<?php

namespace App\Repository;

use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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

    public function findFilteredByState(): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.etat = 1 OR s.etat = 2 OR s.etat = 3 OR s.etat = 4 OR s.etat = 5 OR s.etat = 6')
            ->getQuery()
            ->getResult()
            ;
    }

    public function search(string $search): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.nom LIKE :search')
            ->setParameter('search',"%".$search."%")
            ->andWhere('s.etat = 1 OR s.etat = 2 OR s.etat = 3 OR s.etat = 4 OR s.etat = 5 OR s.etat = 6')
            ->getQuery()
            ->getResult()
        ;
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
