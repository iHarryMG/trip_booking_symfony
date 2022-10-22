<?php

namespace App\Repository;

use App\Entity\FlightInfo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FlightInfo|null find($id, $lockMode = null, $lockVersion = null)
 * @method FlightInfo|null findOneBy(array $criteria, array $orderBy = null)
 * @method FlightInfo[]    findAll()
 * @method FlightInfo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FlightInfoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FlightInfo::class);
    }

    // /**
    //  * @return FlightInfo[] Returns an array of FlightInfo objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FlightInfo
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
