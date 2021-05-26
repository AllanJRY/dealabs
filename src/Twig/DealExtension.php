<?php
namespace App\Twig;

use App\Entity\Deal;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class DealExtension extends AbstractExtension
{
    public function getFilters()
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
}
