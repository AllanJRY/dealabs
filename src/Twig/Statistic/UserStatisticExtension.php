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
            new TwigFunction('stat_nb_published_deals', [$this, 'getNbPublishedDeals']),
            new TwigFunction('stat_nb_published_comments', [$this, 'getNbPublishedComments']),
            new TwigFunction('stat_hottest_published_deal_rate', [$this, 'getHottestPublishedDealRate']),
        ];
    }

    public function getNbPublishedDeals($user): int
    {
        if ($user === null) return 0;

        $result = $this->userRepository->countPublishedDeals($user);

        return $result !== null ? $result['nbPublishedDeals'] : 0;
    }

    public function getNbPublishedComments($user): int
    {
        if ($user === null) return 0;

        $result = $this->userRepository->countPublishedComments($user);

        return $result !== null ? $result['nbPublishedComments'] : 0;
    }

    public function getHottestPublishedDealRate($user): int
    {
        if ($user === null) return 0;

        $result = $this->userRepository->findPublishedDealsHottestRate($user);

        return $result !== null ? $result['hot_value'] : 0;
    }
}
