<?php

namespace App\Repository;

use App\Entity\Deal;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Deal|null find($id, $lockMode = null, $lockVersion = null)
 * @method Deal|null findOneBy(array $criteria, array $orderBy = null)
 * @method Deal[]    findAll()
 * @method Deal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DealRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Deal::class);
    }

    public function findAllOrderByRatingDesc(): ?array
    {
        return $this->createQueryBuilder('d')
            ->addSelect('sum(r.value) as HIDDEN hot_value')
            ->leftJoin('d.ratings', 'r')
            ->where('d.expired != 1')
            ->orderBy('hot_value',  'DESC')
            ->groupBy('d.id')
            ->getQuery()
            ->getResult();
    }

    public function findAllOrderByCreatedAtDesc(): ?array
    {
        return $this->createQueryBuilder('d')
            ->where('d.expired != 1')
            ->orderBy('d.createdAt',  'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findAllHotOrderByDateDesc(): ?array
    {
        return $this->createQueryBuilder('d')
            ->addSelect('sum(r.value) as HIDDEN hot_value')
            ->leftJoin('d.ratings', 'r')
            ->where('d.expired != 1')
            ->having('hot_value > = :min_hot_value')
            ->setParameter('min_hot_value', Deal::MIN_HOT_VALUE)
            ->orderBy('d.createdAt', 'DESC')
            ->groupBy('d.id')
            ->getQuery()
            ->getResult();
    }

    public function findOneWeekDealOrderByComment(): ?array
    {
        $now = new DateTime();
        $week = $now->modify('+1 week');

        return $this->createQueryBuilder('d')
            ->select('COUNT(u) AS HIDDEN nbrComment', 'd')
//            ->where('d.createdAt BETWEEN :firstDate AND :lastDate')
//            ->setParameter('firstDate', $now)
//            ->setParameter('lastDate', $week)
            ->leftJoin('d.comments', 'u')
            ->orderBy('nbrComment', 'DESC')
            ->groupBy('d')
            ->getQuery()
            ->getResult();
    }

    public function findAllWhichContains(String $query) {
        return $this->createQueryBuilder('d')
            ->where("d.expired != 1")
            ->andWhere("d.title LIKE :query OR d.description LIKE :query")
            ->setParameter('query', '%'.$query.'%')
            ->orderBy('d.createdAt', 'DESC')
            ->groupBy('d.id')
            ->getQuery()
            ->getResult();
    }


    // /**
    //  * @return Deal[] Returns an array of Deal objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Deal
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
