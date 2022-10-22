<?php

namespace App\Repository;

use App\Entity\OrderTrip;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OrderTrip|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderTrip|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderTrip[]    findAll()
 * @method OrderTrip[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderTripRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderTrip::class);
    }

    // /**
    //  * @return OrderTrip[] Returns an array of OrderTrip objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OrderTrip
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
