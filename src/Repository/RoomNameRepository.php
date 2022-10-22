<?php

namespace App\Repository;

use App\Entity\RoomName;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RoomName|null find($id, $lockMode = null, $lockVersion = null)
 * @method RoomName|null findOneBy(array $criteria, array $orderBy = null)
 * @method RoomName[]    findAll()
 * @method RoomName[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoomNameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RoomName::class);
    }

    // /**
    //  * @return RoomName[] Returns an array of RoomName objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RoomName
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
