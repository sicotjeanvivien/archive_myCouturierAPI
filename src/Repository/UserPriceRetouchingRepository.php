<?php

namespace App\Repository;

use App\Entity\UserPriceRetouching;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method UserPriceRetouching|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserPriceRetouching|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserPriceRetouching[]    findAll()
 * @method UserPriceRetouching[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserPriceRetouchingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserPriceRetouching::class);
    }

    // /**
    //  * @return UserPriceRetouching[] Returns an array of UserPriceRetouching objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserPriceRetouching
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
