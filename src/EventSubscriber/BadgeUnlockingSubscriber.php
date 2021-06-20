<?php


namespace App\EventSubscriber;


use App\Entity\Badge;
use App\Event\CommentPublishedEvent;
use App\Event\DealCreatedEvent;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BadgeUnlockingSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * BadgeUnlockingSubscriber constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    public static function getSubscribedEvents()
    {
        return [
            DealCreatedEvent::NAME => 'handleDealCreation',
            CommentPublishedEvent::NAME => 'handleCommentPublished',
        ];
    }

    public function handleDealCreation(DealCreatedEvent $event) {
        $author = $event->getDeal()->getAuthor();
        $unlockableBadge = $this->entityManager->getRepository(Badge::class)->findBy(['title' => Badge::COBAYE_BADGE_TITLE], null, 1)[0];

        if (!$author->getBadges()->contains($unlockableBadge) && count($author->getDeals()) >= 10) {
            $author->addBadge($unlockableBadge);
            $this->entityManager->flush();
        }
    }

    public function handleCommentPublished(CommentPublishedEvent $event) {
        $author = $event->getComment()->getAuthor();
        $unlockableBadge = $this->entityManager->getRepository(Badge::class)->findBy(['title' => Badge::RAPPORT_STAGE_BADGE_TITLE], null, 1)[0];

        if (!$author->getComments()->contains($unlockableBadge) && count($author->getComments()) >= 10) {
            $author->addBadge($unlockableBadge);
            $this->entityManager->flush();
        }
    }
}
