<?php

namespace App\Repository;

use App\Entity\Prestations;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;

/**
 * @method Prestations|null find($id, $lockMode = null, $lockVersion = null)
 * @method Prestations|null findOneBy(array $criteria, array $orderBy = null)
 * @method Prestations[]    findAll()
 * @method Prestations[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrestationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Prestations::class);
    }

    // /**
    //  * @return Prestations[] Returns an array of Prestations objects
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
    public function findOneBySomeField($value): ?Prestations
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findPrestaByClientState($userApp, $state)
    {
        $query = $this->getEntityManager()->createQuery(
            "SELECT DISTINCT p.id, p.accept, p.pay, r.type
            FROM App\Entity\Prestations p
            JOIN p.client c
            JOIN p.userPriceRetouching upr
            JOIN upr.Retouching r
            JOIN p.prestationHistories ph
            JOIN ph.statut s
            WHERE p.state = :state AND c.id = :userapp "
        )->setParameters([
            'userapp' => $userApp,
            'state' => $state
        ]);
        return $query->getResult();
    }
    
    public function findPrestaByCouturierState($userApp, $state)
    {
        $query = $this->getEntityManager()->createQuery(
            "SELECT DISTINCT p.id, p.accept, p.pay, r.type
            FROM App\Entity\Prestations p
            JOIN p.prestationHistories ph
            JOIN ph.statut s
            JOIN p.userPriceRetouching upr
            JOIN upr.Retouching r
            JOIN upr.UserApp u
            WHERE p.state = :state AND u.id = :userapp "
        )->setParameters([
            'userapp' => $userApp,
            'state' => $state
        ]);
        return $query->getResult();
    }

    public function findlastStatutByUserApp($userapp, $state)
    {
        $query = $this->getEntityManager()->createQuery(
            "SELECT c.username
            FROM App\Entity\Prestations p
            JOIN p.client c
            WHERE p.state = :state AND c.id = :userapp "
        )->setParameters([
            'userapp' => $userapp,
            'state' => $state
        ]);
        return $query->getResult();
    }
}
