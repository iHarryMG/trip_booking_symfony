<?php

namespace App\Repository;

use App\Entity\CarInfo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CarInfo|null find($id, $lockMode = null, $lockVersion = null)
 * @method CarInfo|null findOneBy(array $criteria, array $orderBy = null)
 * @method CarInfo[]    findAll()
 * @method CarInfo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CarInfoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CarInfo::class);
    }

    // /**
    //  * @return CarInfo[] Returns an array of CarInfo objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CarInfo
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
