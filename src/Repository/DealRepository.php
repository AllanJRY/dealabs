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
            ->addSelect('SUM(r.value) as HIDDEN hot_value')
            ->leftJoin('d.ratings', 'r')
            ->where('d.expired != 1')
            ->orderBy('hot_value', 'DESC')
            ->groupBy('d.id')
            ->getQuery()
            ->getResult();
    }

    public function findAllDealOneWeekByCreatedAtDesc(): ?array
    {
        $now = new DateTime();
        $week = $now->modify('+1 week');

        return $this->createQueryBuilder('d')
            ->select('COUNT(u) AS HIDDEN nbrComment', 'd')
            ->where('d.createdAt <= :lastDate')
            ->setParameter('lastDate', $week)
            ->leftJoin('d.comments', 'u')
            ->orderBy('nbrComment', 'DESC')
            ->groupBy('d')
            ->getQuery()
            ->getResult();
    }

    public function findAllDealOneDayByRatingDesc(): ?array
    {
        $now = new DateTime();
        $day = $now->modify('+1 day');

        return $this->createQueryBuilder('d')
            ->addSelect('SUM(r.value) as HIDDEN hot_value')
            ->leftJoin('d.ratings', 'r')
            ->where('d.expired != 1')
            ->having('hot_value > = :min_hot_value')
            ->setParameter('min_hot_value', Deal::MIN_HOT_VALUE)
            ->where('d.createdAt <= :lastDate')
            ->setParameter('lastDate', $day)
            ->orderBy('hot_value', 'DESC')
            ->groupBy('d.id')
            ->getQuery()
            ->getResult();
    }

    public function findAllHotOrderByDateDesc(): ?array
    {
        return $this->createQueryBuilder('d')
            ->addSelect('SUM(r.value) as HIDDEN hot_value')
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
            ->where('d.createdAt <= :lastDate')
            ->setParameter('lastDate', $week)
            ->leftJoin('d.comments', 'u')
            ->orderBy('nbrComment', 'DESC')
            ->groupBy('d')
            ->getQuery()
            ->getResult();
    }

    public function findAllWhichContains(string $query)
    {
        return $this->createQueryBuilder('d')
            ->where("d.expired != 1")
            ->andWhere("d.title LIKE :query OR d.description LIKE :query")
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('d.createdAt', 'DESC')
            ->groupBy('d.id')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $query
     * @param int|null $limit
     * @return int|mixed|string
     */
    public function findWhichContains(string $query, int $limit = null)
    {
        if ($limit == null) return $this->findAllWhichContains($query);

        return $this->createQueryBuilder('d')
            ->where("d.expired != 1")
            ->andWhere("d.title LIKE :query OR d.description LIKE :query")
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('d.createdAt', 'DESC')
            ->groupBy('d.id')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $user
     * @return int|mixed|string
     */
    public function findBestRatingDealByUser($user)
    {
        return $this->createQueryBuilder('d')
            ->addSelect('SUM(r.value) as value')
            ->addSelect('d, MAX(d.value) as hot_value')
//            ->select('max(hot_value)')
            ->leftJoin('d.ratings', 'r')
            ->where('d.expired != 1')
            ->where('d.author = :user')
            ->setParameter('user', $user)
//            ->orderBy('hot_value', 'DESC')
//            ->groupBy('d.id')
            ->getQuery()
            ->getResult();
    }

    public function findNewDealByAlarmUserOrderByDateDesc($user)
    {
        $final = [];
        foreach ($user->getAlarms() as $alarm) {
            $res = $this->createQueryBuilder('d')
                ->addSelect('d, SUM(r.value) as HIDDEN hot_value')
                ->leftJoin('d.ratings', 'r')
                ->where('d.expired != 1')
                ->andWhere("d.title LIKE :query OR d.description LIKE :query")
                ->setParameter('query', '%' . $alarm->getSearch() . '%')
                ->having('hot_value >= :min_hot_value')
                ->setParameter('min_hot_value', $alarm->getRating())
                ->orderBy('d.createdAt', 'DESC')
                ->groupBy('d.id')
                ->getQuery()
                ->getResult();
            $final = array_merge(array_merge($final, $res));
        }
        $this->remove_duplicate_models($final);
        return $final;
    }

    public function findNumberNewDealByAlarmUserAndTime($user, $time)
    {
        $final = [];
        foreach ($user->getAlarms() as $alarm) {
            $res = $this->createQueryBuilder('d')
                ->addSelect('d, SUM(r.value) as HIDDEN hot_value')
                ->leftJoin('d.ratings', 'r')
                ->where('d.expired != 1')
                ->where('d.createdAt <= :lastDate')
                ->setParameter('lastDate', $time)
                ->andWhere("d.title LIKE :query OR d.description LIKE :query")
                ->setParameter('query', '%' . $alarm->getSearch() . '%')
                ->having('hot_value >= :min_hot_value')
                ->setParameter('min_hot_value', $alarm->getRating())
                ->groupBy('d.id')
                ->getQuery()
                ->getResult();
            $final = array_merge(array_merge($final, $res));
        }
        $this->remove_duplicate_models($final);
        return count($final);
    }

    public function findNewDealOneDayByAlarmUserOrderByDateDesc($user)
    {
        $now = new DateTime();
        $day = $now->modify('+1 day');

        $final = [];
        foreach ($user->getAlarms() as $alarm) {
            $res = $this->createQueryBuilder('d')
                ->addSelect('d, SUM(r.value) as HIDDEN hot_value')
                ->leftJoin('d.ratings', 'r')
                ->where('d.expired != 1')
                ->where('d.createdAt <= :lastDate')
                ->setParameter('lastDate', $day)
                ->andWhere("d.title LIKE :query OR d.description LIKE :query")
                ->setParameter('query', '%' . $alarm->getSearch() . '%')
                ->having('hot_value >= :min_hot_value')
                ->setParameter('min_hot_value', $alarm->getRating())
                ->orderBy('d.createdAt', 'DESC')
                ->groupBy('d.id')
                ->getQuery()
                ->getResult();
            $final = array_merge(array_merge($final, $res));
        }
        $this->remove_duplicate_models($final);
        return $final;
    }

    function remove_duplicate_models($cars)
    {
        $models = array_map(function ($car) {
            return $car->getId();
        }, $cars);

        $unique_models = array_unique($models);

        return array_values(array_intersect_key($cars, $unique_models));
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
