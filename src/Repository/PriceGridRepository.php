<?php

namespace App\Repository;

use App\Entity\PriceGrid;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PriceGrid|null find($id, $lockMode = null, $lockVersion = null)
 * @method PriceGrid|null findOneBy(array $criteria, array $orderBy = null)
 * @method PriceGrid[]    findAll()
 * @method PriceGrid[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PriceGridRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PriceGrid::class);
    }

    public function findCommission($priceCouturier)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            "SELECT p.commission
            FROM App\Entity\PriceGrid p
            WHERE p.min <= :price AND p.max > :price
        "
        )->setParameter('price', $priceCouturier);
        dump($query->getOneOrNullResult());
        return $query->getOneOrNullResult();
    }

    // /**
    //  * @return PriceGrid[] Returns an array of PriceGrid objects
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
    public function findOneBySomeField($value): ?PriceGrid
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
