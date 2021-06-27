<?php

namespace App\Event;

use App\Entity\Deal;
use Symfony\Contracts\EventDispatcher\Event;

class DealCreatedEvent extends Event
{
    public const NAME = 'deal.created';

    /**
     * @var Deal
     */
    private $deal;

    /**
     * DealCreatedEvent constructor.
     * @param Deal $deal
     */
    public function __construct(Deal $deal)
    {
        $this->deal = $deal;
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

}
