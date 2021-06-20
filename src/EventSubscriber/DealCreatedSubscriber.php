<?php


namespace App\EventSubscriber;


use App\Event\DealCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DealCreatedSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [
            DealCreatedEvent::NAME => 'handleDealCreation',
        ];
    }

    public function handleDealCreation(DealCreatedEvent $event) {
        dump($event);
    }
}
