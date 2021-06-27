<?php

namespace App\Twig\Statistic;

use App\Entity\User;
use App\Repository\UserRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UserStatisticExtension extends AbstractExtension
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('stat_nb_users', [$this, 'getNbUsers']),
            new TwigFunction('stat_nb_published_deals', [$this, 'getNbPublishedDeals']),
            new TwigFunction('stat_nb_published_comments', [$this, 'getNbPublishedComments']),
            new TwigFunction('stat_hottest_published_deal_rate', [$this, 'getHottestPublishedDealRate']),
            new TwigFunction('stat_avg_deal_rates_on_time_window', [$this, 'getAvgDealRatesOnTimeWindow']),
            new TwigFunction('stat_percent_of_hot_deals', [$this, 'getPercentOfHotDeals']),
        ];
    }

    public function getNbUsers(): int
    {
        return count($this->userRepository->findAll());
    }

    public function getNbPublishedDeals(User $user): int
    {
        if ($user === null) return 0;

        $result = $this->userRepository->countPublishedDeals($user);

        return $result !== null && $result['nbPublishedDeals'] !== null ? $result['nbPublishedDeals'] : 0;
    }

    public function getNbPublishedComments(User $user): int
    {
        if ($user === null) return 0;

        $result = $this->userRepository->countPublishedComments($user);

        return $result !== null && $result['nbPublishedComments'] !== null ? $result['nbPublishedComments'] : 0;
    }

    public function getHottestPublishedDealRate(User $user): int
    {
        if ($user === null) return 0;

        $result = $this->userRepository->findPublishedDealsHottestRate($user);

        return $result !== null && $result['hot_value'] !== null ? $result['hot_value'] : 0;
    }

    /**
     * Return rates average of user published deals on a year back.
     *
     * @param User $user
     * @return int
     */
    public function getAvgDealRatesOnTimeWindow(User $user): int
    {
        if ($user === null) return 0;

        $result = $this->userRepository->findAvgDealRatesOnYear($user);

        return $result !== null && $result['avg_hot_value'] !== null ? $result['avg_hot_value'] : 0;
    }

    /**
     * Return rates average of user published deals on a year back.
     *
     * @param User $user
     * @return int
     */
    public function getPercentOfHotDeals(User $user): int
    {
        if ($user === null) return 0;

        $result = $this->userRepository->findPercentOfHotDeals($user);

        return $result !== null && $result['percentage'] !== null ? $result['percentage'] : 0;
    }
}
