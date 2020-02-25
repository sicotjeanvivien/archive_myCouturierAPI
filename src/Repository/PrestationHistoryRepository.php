<?php

namespace App\Repository;

use App\Entity\PrestationHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PrestationHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrestationHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrestationHistory[]    findAll()
 * @method PrestationHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrestationHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrestationHistory::class);
    }

    // /**
    //  * @return PrestationHistory[] Returns an array of PrestationHistory objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PrestationHistory
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
