<?php

namespace App\Twig;

use App\Entity\Deal;
use App\Entity\User;
use App\Repository\DealRepository;
use Symfony\Component\Security\Core\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;

class DealExtension extends AbstractExtension
{
    /**
     * @var DealRepository
     */
    private $dealRepository;
    /**
     * @var Security
     */
    private $security;

    public function __construct(DealRepository $dealRepository, Security $security)
    {
        $this->security = $security;
        $this->dealRepository = $dealRepository;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_all_deal', [$this, 'getAllDeal']),
            new TwigFunction('get_all_hot_deal', [$this, 'getAllHotDeal']),
            new TwigFunction('get_all_hot_deal_user', [$this, 'getAllHotDealUser']),
            new TwigFunction('get_all_hot_deal_one_day', [$this, 'getAllDealOneDayByRatingDesc']),
            new TwigFunction('get_number_new_deal_last_time_request_keyword_alarms', [$this, 'getNumberNewDealLastTimeRequestKeywordAlarms']),
        ];
    }

    public function getNumberNewDealLastTimeRequestKeywordAlarms($time): int
    {
        return $this->dealRepository->findNumberNewDealByAlarmUserAndTime($this->security->getUser(), $time);
    }

    public function getAllDeal(): array
    {
        return $this->dealRepository->findAll();
    }

    public function getAllHotDeal(): array
    {
        return $this->dealRepository->findAllHotOrderByDateDesc();
    }

    public function getAllHotDealUser(User $id): array
    {
        return $this->dealRepository->findAllMaxHotDeal($id);
    }

    public function getAllDealOneDayByRatingDesc(): ?array
    {
        return $this->dealRepository->findAllDealOneDayByRatingDesc();
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('display_hot_value', [$this, 'calcHotValue']),
        ];
    }

    /**
     * Calculate the hot value of a deal by sum up rating's values.
     *
     * @param Deal $deal
     * @return int|mixed
     */
    public function calcHotValue(Deal $deal)
    {
        $hotValue = 0;
        foreach ($deal->getRatings() as $dealRating) {
            $hotValue += $dealRating->getValue();
        }

        return $hotValue;
    }

    /**
     * @return TwigTest[]
     */
    public function getTests(): array
    {
        return [
            new TwigTest('instanceof', [$this, 'isInstanceof'])
        ];
    }

    /**
     * @param $var
     * @param $instance
     * @return bool
     */
    public function isInstanceof($var, $instance): bool
    {
        return $var instanceof $instance;
    }
}
