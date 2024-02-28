<?php

namespace App\Repository;

use App\Entity\Lieu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Lieu>
 *
 * @method Lieu|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lieu|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lieu[]    findAll()
 * @method Lieu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LieuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lieu::class);
    }

    public function findByText($text) : array
    {
        return $this->createQueryBuilder('l')
            ->where('l.nom LIKE :text')
            ->setParameter('text', '%' . $text . '%')
            ->getQuery()
            ->getResult();
    }

    public function findByTextAndStateAndVille($text, $actif, $ville) : array
    {

        $query = $this->createQueryBuilder('l')
            ->where('l.nom LIKE :text')
            ->setParameter('text', '%' . $text . '%');

        if ($ville != 0) {
            $query->andWhere('l.ville = :id')
                ->setParameter('id', $ville);
        }

        if ($actif) {
            $query->andWhere('l.actif = :value')
                ->setParameter('value', 1);
        }

        return $query->getQuery()->getResult();
    }

//    /**
//     * @return Lieu[] Returns an array of Lieu objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Lieu
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
