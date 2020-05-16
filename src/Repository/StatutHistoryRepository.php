<?php

namespace App\Repository;

use App\Entity\StatutHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method StatutHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method StatutHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method StatutHistory[]    findAll()
 * @method StatutHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatutHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StatutHistory::class);
    }
}
