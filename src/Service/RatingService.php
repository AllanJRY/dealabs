<?php


namespace App\Service;


use App\Entity\Deal;
use App\Entity\Rating;
use App\Entity\User;
use App\Repository\DealRepository;
use Doctrine\ORM\EntityManagerInterface;

class RatingService
{
    private $dealRepository;
    private $entityManager;


    /**
     * RatingService constructor.
     * @param EntityManagerInterface $entityManager
     * @param DealRepository $dealRepository
     */
    public function __construct(EntityManagerInterface $entityManager, DealRepository $dealRepository)
    {
        $this->entityManager = $entityManager;
        $this->dealRepository = $dealRepository;
    }


    /**
     * Try to find a rating of the user which is applied to the given deal.
     *
     * If a rating is found, it will be return, false is returned otherwise.
     *
     * @param User $user
     * @param Deal $deal
     * @return bool
     */
    public function tryFindUserRatingOfDeal(User $user, Deal $deal)
    {
        foreach ($user->getRatings() as $rating) {
            if ($rating->getDeal() === $deal) {
                return $rating;
            }
        }

        return false;
    }

    /**
     * Update the value of a rating.
     * Perform a check on the current rating value to update only if values are different.
     *
     * @param Rating $rating
     * @param int $value
     */
    public function updateRatingValue(Rating $rating, int $value)
    {
        if ($rating->getValue() !== $value) {
            $rating->setValue($value);
            $this->entityManager->flush();
        }
        //TODO remove if same value ?
    }

    /**
     * Create a new rating between the user and the deal.
     *
     * Does NOT perform a check to find if the user already rated the deal.
     *
     * @param User $user
     * @param Deal $deal
     * @param int $value
     */
    public function createRating(User $user, Deal $deal, int $value)
    {
        $rating = new Rating();
        $rating->setRater($user)
            ->setDeal($deal)
            ->setValue($value);
        $this->entityManager->persist($rating);
        $this->entityManager->flush();
    }
}