<?php

namespace App\Repository;

use App\Entity\CarPrice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CarPrice|null find($id, $lockMode = null, $lockVersion = null)
 * @method CarPrice|null findOneBy(array $criteria, array $orderBy = null)
 * @method CarPrice[]    findAll()
 * @method CarPrice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CarPriceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CarPrice::class);
    }

    // /**
    //  * @return CarPrice[] Returns an array of CarPrice objects
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
    public function findOneBySomeField($value): ?CarPrice
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
