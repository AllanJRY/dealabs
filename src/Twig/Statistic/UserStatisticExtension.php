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
            new TwigFunction('stat_nb_published_deals', [$this, 'getPublishedDeal']),
        ];
    }

    public function getPublishedDeal($user): int
    {
        if ($user === null) return 0;

        $result = $this->userRepository->countNbPublishedDeals($user);
        dump($result);

        return $result['nbDealsPublished'];
    }
}
