<?php
namespace App\Twig;

use App\Entity\Deal;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigTest;

class DealExtension extends AbstractExtension
{
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
