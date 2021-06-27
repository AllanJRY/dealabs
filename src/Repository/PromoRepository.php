<?php

namespace App\Repository;

use App\Entity\Deal;
use App\Entity\Promo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Promo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Promo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Promo[]    findAll()
 * @method Promo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PromoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Promo::class);
    }

    public function findAllOrderByRatingDesc(): ?array
    {
        return $this->createQueryBuilder('p')
            ->addSelect('sum(r.value) as HIDDEN hot_value')
            ->leftJoin('p.ratings', 'r')
            ->where('p.expired != 1')
            ->orderBy('hot_value', 'DESC')
            ->groupBy('p.id')
            ->getQuery()
            ->getResult();
    }

    public function findAllOrderByCreatedAtDesc(): ?array
    {
        return $this->createQueryBuilder('p')
            ->where('p.expired != 1')
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findAllHotOrderByRatingDesc(): ?array
    {
        return $this->createQueryBuilder('p')
            ->addSelect('sum(r.value) as HIDDEN hot_value')
            ->leftJoin('p.ratings', 'r')
            ->where('p.expired != 1')
            ->having('hot_value > = :min_hot_value')
            ->setParameter('min_hot_value', Deal::MIN_HOT_VALUE)
            ->orderBy('hot_value', 'DESC')
            ->groupBy('p.id')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Promo[] Returns an array of Promo objects
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
    public function findOneBySomeField($value): ?Promo
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
