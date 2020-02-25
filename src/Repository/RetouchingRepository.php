<?php

namespace App\Repository;

use App\Entity\Retouching;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Retouching|null find($id, $lockMode = null, $lockVersion = null)
 * @method Retouching|null findOneBy(array $criteria, array $orderBy = null)
 * @method Retouching[]    findAll()
 * @method Retouching[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RetouchingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Retouching::class);
    }

    // /**
    //  * @return Retouching[] Returns an array of Retouching objects
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
    public function findOneBySomeField($value): ?Retouching
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
