<?php

namespace App\Repository;

use App\Entity\User;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use function Doctrine\ORM\QueryBuilder;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
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

    public function countPublishedDeals(User $user): ?array
    {
        return $this->createQueryBuilder('u')
            ->select('COUNT(d) AS nbPublishedDeals')
            ->where('u.id = :id')
            ->setParameter('id', $user->getId())
            ->leftJoin('u.deals', 'd')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function countPublishedComments(User $user): ?array
    {
        return $this->createQueryBuilder('u')
            ->select('COUNT(c) AS nbPublishedComments')
            ->where('u.id = :id')
            ->setParameter('id', $user->getId())
            ->leftJoin('u.comments', 'c')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findPublishedDealsHottestRate(User $user): ?array
    {
        return $this->createQueryBuilder('u')
            ->select('SUM(r.value) AS hot_value')
            ->where('u.id = :id')
            ->setParameter('id', $user->getId())
            ->innerJoin('u.deals', 'd')
            ->innerJoin('d.ratings', 'r')
            ->orderBy('hot_value', 'DESC')
            ->groupBy('d.id')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * This function calculate the average rate of deals published by the user during a year back.
     *
     * @param User $user
     * @return array|null
     */
    public function findAvgDealRatesOnYear(User $user): ?array
    {
        $qb = $this->createQueryBuilder('u');
        return $qb->select('SUM(r.value)/COUNT(r.value) AS avg_hot_value')
            ->innerJoin('u.deals', 'd')
            ->innerJoin('d.ratings', 'r')
            ->where('u.id = :id')
            ->andWhere("d.createdAt > DATE_SUB(CURRENT_DATE(), 1, 'year')")
            ->setParameter('id', $user->getId())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // /**
    //  * @return User[] Returns an array of User objects
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
    public function findOneBySomeField($value): ?User
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
