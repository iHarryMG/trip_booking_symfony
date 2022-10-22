<?php

namespace App\Repository;

use App\Entity\TripPackage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TripPackage|null find($id, $lockMode = null, $lockVersion = null)
 * @method TripPackage|null findOneBy(array $criteria, array $orderBy = null)
 * @method TripPackage[]    findAll()
 * @method TripPackage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TripPackageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TripPackage::class);
    }

    // /**
    //  * @return TripPackage[] Returns an array of TripPackage objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TripPackage
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
