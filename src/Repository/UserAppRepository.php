<?php

namespace App\Repository;

use App\Entity\UserApp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method UserApp|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserApp|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserApp[]    findAll()
 * @method UserApp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserAppRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserApp::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function countUsername($username, $id)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            "SELECT COUNT(a)
            FROM App\Entity\UserApp a
            WHERE (a.username = :username AND a.id != :id) OR (a.username = :username AND a.id != 'null')
        "
        )->setParameters([
            'username' => $username,
            'id' => $id
        ]);

        // returns an array of Product objects
        return $query->getSingleScalarResult();
    }

    public function findCouturierBy($longitude, $latitude, $retouche, $radius, $userId)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            "SELECT a
            FROM App\Entity\UserApp a
            JOIN a.userPriceRetouchings ur
            JOIN ur.Retouching r 
            JOIN ur.UserApp ua
            WHERE a.activeCouturier = true AND r.type = :retouche AND ua.id <> :userId 
            AND (a.longitude BETWEEN (:longitude - :radius) AND (:longitude + :radius)) 
            AND (a.latitude BETWEEN (:latitude - :radius)  AND (:latitude + :radius))
        "
        )->setParameters([
            'longitude' => $longitude,
            'latitude' => $latitude,
            'radius' => $radius,
            'retouche' => $retouche,
            'userId' => $userId
        ]);

        // returns an array of Product objects
        return $query->getResult();
    }

    public function findAllCouturierBy($longitude, $latitude, $radius)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            "SELECT a
            FROM App\Entity\UserApp a
            JOIN a.userPriceRetouchings ur
            JOIN ur.Retouching r 
            WHERE a.activeCouturier = true  
            AND (a.longitude BETWEEN (:longitude - :radius) AND (:longitude + :radius)) 
            AND (a.latitude BETWEEN (:latitude - :radius)  AND (:latitude + :radius))
        "
        )->setParameters([
            'longitude' => $longitude,
            'latitude' => $latitude,
            'radius' => $radius,
        ]);

        // returns an array of Product objects
        return $query->getResult();
    }

    public function countUserByEmail($email, $userId)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            "SELECT count(a)
            FROM App\Entity\UserApp a
            WHERE a.email = :email AND a.id = :userId
        "
        )->setParameters(['email' => $email, 'userId' => $userId]);

        // returns an array of Product objects
        return $query->getSingleScalarResult();
    }
}

    // /**
    //  * @return UserApp[] Returns an array of UserApp objects
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
    public function findOneBySomeField($value): ?UserApp
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
