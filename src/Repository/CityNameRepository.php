<?php

namespace App\Repository;

use App\Entity\CityName;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
#use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CityName|null find($id, $lockMode = null, $lockVersion = null)
 * @method CityName|null findOneBy(array $criteria, array $orderBy = null)
 * @method CityName[]    findAll()
 * @method CityName[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CityNameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CityName::class);
    }

    
    /**
      * @param int $country_id
      * @return City[]
      */

    public function getCityList($country_id): array
    {
        try{
            $qb = $this->createQueryBuilder('p')
                ->andWhere('p.country_id = :countryId')
                ->setParameter('countryId', $country_id)
                ->orderBy('p.id', 'ASC')
                ->getQuery();

            return $qb->execute();
        
        }catch(Exception $ex){
            return null;
        }
    }
    
    // /**
    //  * @return CityName[] Returns an array of CityName objects
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
    public function findOneBySomeField($value): ?CityName
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
