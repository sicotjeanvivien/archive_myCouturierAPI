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

    public function findAllRetouche()
    {
        $query = $this->getEntityManager()->createQuery(
            "SELECT r.id, r.type, r.description, cr.type as category
            FROM App\Entity\Retouching r
            JOIN r.CategoryRetouching cr
            "
        );
        return $query->getResult();
        
    }
}
