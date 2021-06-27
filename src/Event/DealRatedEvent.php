<?php

namespace App\Event;

use App\Entity\Deal;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class DealRatedEvent extends Event
{
    public const NAME = 'deal.rated';

    /**
     * @var Deal
     */
    private $deal;

    /**
     * @var User
     */
    private $rater;

    /**
     * DealCreatedEvent constructor.
     * @param Deal $deal
     * @param User $rater
     */
    public function __construct(Deal $deal, User $rater)
    {
        $this->deal = $deal;
        $this->rater = $rater;
    }

    /**
     * @return Deal
     */
    public function getDeal(): Deal
    {
        return $this->deal;
    }

    /**
     * @param Deal $deal
     */
    public function setDeal(Deal $deal): void
    {
        $this->deal = $deal;
    }

    /**
     * @return User
     */
    public function getRater(): User
    {
        return $this->rater;
    }

    /**
     * @param User $rater
     */
    public function setRater(User $rater): void
    {
        $this->rater = $rater;
    }
}
