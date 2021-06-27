<?php


namespace App\EventSubscriber;


use App\Entity\Badge;
use App\Event\CommentPublishedEvent;
use App\Event\DealCreatedEvent;
use App\Event\DealRatedEvent;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BadgeUnlockingSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * BadgeUnlockingSubscriber constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }


    public static function getSubscribedEvents()
    {
        return [
            DealCreatedEvent::NAME => 'handleDealCreation',
            CommentPublishedEvent::NAME => 'handleCommentPublished',
            DealRatedEvent::NAME => 'handleDealRating',
        ];
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function handleDealCreation(DealCreatedEvent $event)
    {
        $author = $event->getDeal()->getAuthor();
        $unlockableBadge = $this->entityManager->getRepository(Badge::class)->findBy(['title' => Badge::COBAYE_BADGE_TITLE], null, 1)[0];

        if (!$this->isAlreadyUnlocked($unlockableBadge, $author->getBadges()) && count($author->getDeals()) >= 10) {
            $author->addBadge($unlockableBadge);
            $this->entityManager->flush();
        }
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function handleCommentPublished(CommentPublishedEvent $event)
    {
        $author = $event->getComment()->getAuthor();
        $unlockableBadge = $this->entityManager->getRepository(Badge::class)->findBy(['title' => Badge::RAPPORT_STAGE_BADGE_TITLE], null, 1)[0];

        if (!$this->isAlreadyUnlocked($unlockableBadge, $author->getBadges()) && count($author->getComments()) >= 10) {
            $author->addBadge($unlockableBadge);
            $this->entityManager->flush();
        }
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function handleDealRating(DealRatedEvent $event)
    {
        $rater = $event->getRater();
        $unlockableBadge = $this->entityManager->getRepository(Badge::class)->findBy(['title' => Badge::SURVEILLANT_BADGE_TITLE], null, 1)[0];

        if (!$this->isAlreadyUnlocked($unlockableBadge, $rater->getBadges()) && count($rater->getRatings()) >= 10) {
            $rater->addBadge($unlockableBadge);
            $this->entityManager->flush();
        }
    }

    private function isAlreadyUnlocked(Badge $badge, Collection $userBadges): bool
    {
        foreach ($userBadges as $userBadge) {
            if ($userBadge->getId() === $badge->getId()) {
                return true;
            }
        }

        return false;
    }
}
