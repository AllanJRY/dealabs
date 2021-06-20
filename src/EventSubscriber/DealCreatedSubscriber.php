<?php


namespace App\EventSubscriber;


use App\Entity\Badge;
use App\Event\DealCreatedEvent;
use App\Repository\BadgeRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DealCreatedSubscriber implements EventSubscriberInterface
{
    private const BADGE_NAME = "Cobaye";

    /**
     * @var Badge
     */
    private $unlockableBadge;

    /**
     * @var BadgeRepository
     */
    private $badgeRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * DealCreatedSubscriber constructor.
     * @param BadgeRepository $badgeRepository
     * @param EntityManager $entityManager
     */
    public function __construct(BadgeRepository $badgeRepository,EntityManagerInterface $entityManager)
    {
        $this->badgeRepository = $badgeRepository;
        $this->entityManager = $entityManager;
        $this->unlockableBadge =$this->badgeRepository->findBy(['title' => self::BADGE_NAME])[0];
    }


    public static function getSubscribedEvents()
    {
        return [
            DealCreatedEvent::NAME => 'handleDealCreation',
        ];
    }

    public function handleDealCreation(DealCreatedEvent $event) {
        $author = $event->getDeal()->getAuthor();

        if (!$author->getBadges()->contains($this->unlockableBadge) && count($author->getDeals()) >= 10) {
            $author->addBadge($this->unlockableBadge);
            $this->entityManager->flush();
        }
    }
}
