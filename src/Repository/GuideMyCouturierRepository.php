<?php

namespace App\Repository;

use App\Entity\GuideMyCouturier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GuideMyCouturier|null find($id, $lockMode = null, $lockVersion = null)
 * @method GuideMyCouturier|null findOneBy(array $criteria, array $orderBy = null)
 * @method GuideMyCouturier[]    findAll()
 * @method GuideMyCouturier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GuideMyCouturierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GuideMyCouturier::class);
    }

    // /**
    //  * @return GuideMyCouturier[] Returns an array of GuideMyCouturier objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GuideMyCouturier
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
