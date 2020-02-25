<?php

namespace App\Repository;

use App\Entity\CategoryRetouching;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CategoryRetouching|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategoryRetouching|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategoryRetouching[]    findAll()
 * @method CategoryRetouching[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRetouchingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategoryRetouching::class);
    }

    // /**
    //  * @return CategoryRetouching[] Returns an array of CategoryRetouching objects
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
    public function findOneBySomeField($value): ?CategoryRetouching
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
