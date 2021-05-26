<?php

namespace App\Repository;

use App\Entity\Deal;
use App\Entity\GoodPlan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GoodPlan|null find($id, $lockMode = null, $lockVersion = null)
 * @method GoodPlan|null findOneBy(array $criteria, array $orderBy = null)
 * @method GoodPlan[]    findAll()
 * @method GoodPlan[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GoodPlanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GoodPlan::class);
    }

    public function findAllOrderByRatingDesc(): ?array
    {
        return $this->createQueryBuilder('gp')
            ->addSelect('sum(r.value) as HIDDEN hot_value')
            ->leftJoin('gp.ratings', 'r')
            ->orderBy('hot_value',  'DESC')
            ->groupBy('gp.id')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllOrderByCreatedAtDesc(): ?array
    {
        return $this->createQueryBuilder('gp')
            ->orderBy('gp.createdAt',  'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findAllHotOrderByRatingDesc(): ?array
    {
        return $this->createQueryBuilder('gp')
            ->addSelect('sum(r.value) as HIDDEN hot_value')
            ->leftJoin('gp.ratings', 'r')
            ->having('hot_value > = :min_hot_value')
            ->setParameter('min_hot_value', Deal::MIN_HOT_VALUE)
            ->orderBy('hot_value',  'DESC')
            ->groupBy('gp.id')
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return GoodPlan[] Returns an array of GoodPlan objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
