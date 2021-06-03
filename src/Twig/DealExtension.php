<?php
namespace App\Twig;

use App\Entity\Deal;
use App\Repository\CategoryRepository;
use App\Repository\DealRepository;
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

    public function __construct(DealRepository $dealRepository)
    {
        $this->dealRepository = $dealRepository;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_all_deal', [$this, 'getAllDeal']),
            new TwigFunction('get_all_hot_deal', [$this, 'getAllHotDeal']),
        ];
    }

    public function getAllDeal(): array
    {
        return $this->dealRepository->findAll();
    }

    public function getAllHotDeal(): array
    {
        return $this->dealRepository->findAllHotOrderByRatingDesc();
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
        return  $var instanceof $instance;
    }
}