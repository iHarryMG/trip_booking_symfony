<?php

namespace App\Repository;

use App\Entity\HotelPhoto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method HotelPhoto|null find($id, $lockMode = null, $lockVersion = null)
 * @method HotelPhoto|null findOneBy(array $criteria, array $orderBy = null)
 * @method HotelPhoto[]    findAll()
 * @method HotelPhoto[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HotelPhotoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HotelPhoto::class);
    }

     /**
      * @param int $is_special, $is_active
      * @return HotelPhoto[]
      */

    public function getSpecialHotelPhoto($is_special, $is_active): array
    {
        try{

            $qb = $this->createQueryBuilder('t')
                ->join('App:HotelList', 'h', 'WITH', 't.hotel_id = h.id')
                ->where('t.is_special = :isSpecial')
                ->andWhere('t.is_active = :isActive')
                ->setParameter('isSpecial', $is_special)
                ->setParameter('isActive', $is_active)
                ->orderBy('t.id', 'DESC')
                ->getQuery();
    
            return $qb->execute();

        
        }catch(Exception $ex){
            return null;
        }
    }   
    
    // /**
    //  * @return HotelPhoto[] Returns an array of HotelPhoto objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?HotelPhoto
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
