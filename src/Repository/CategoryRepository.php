<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function findOrderByDeals()
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(u) AS HIDDEN nbrDeals', 'c')
            ->leftJoin('c.deals', 'u')
            ->orderBy('nbrDeals', 'DESC')
            ->groupBy('c')
            ->getQuery()
            ->getResult();
    }

    public function findAllWhichContains(string $query)
    {
        return $this->createQueryBuilder('c')
            ->where("c.title LIKE :query")
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('c.title')
            ->getQuery()
            ->getResult();
    }

    public function findWhichContains(string $query, int $limit = null)
    {
        if ($limit == null) return $this->findAllWhichContains($query);

        return $this->createQueryBuilder('c')
            ->where("c.title LIKE :query")
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('c.title')
            ->getQuery()
            ->setMaxResults($limit)
            ->getResult();
    }

    // /**
    //  * @return Category[] Returns an array of Category objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Category
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
