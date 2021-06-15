<?php

namespace App\Repository;

use App\Entity\Partner;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Partner|null find($id, $lockMode = null, $lockVersion = null)
 * @method Partner|null findOneBy(array $criteria, array $orderBy = null)
 * @method Partner[]    findAll()
 * @method Partner[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PartnerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Partner::class);
    }

    public function findOrderByDeals()
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(u) AS HIDDEN nbrDeals', 'p')
            ->leftJoin('p.deals', 'u')
            ->orderBy('nbrDeals', 'DESC')
            ->groupBy('p')
            ->getQuery()
            ->getResult();
    }

    public function findAllWhichContains(String $query) {
        return $this->createQueryBuilder('p')
            ->where("p.name LIKE :query")
            ->setParameter('query', '%'.$query.'%')
            ->orderBy('p.name')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Partner[] Returns an array of Partner objects
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
    public function findOneBySomeField($value): ?Partner
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
