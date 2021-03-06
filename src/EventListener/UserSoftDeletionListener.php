<?php

namespace App\EventListener;

use App\Entity\User;
use DateTime;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserSoftDeletionListener
{
    private const ANONYMOUS_PREFIX = "anonyme";

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * UserSoftDeletionListener constructor.
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }


    public function preUpdate(PreUpdateEventArgs $event): void
    {
        $user = $event->getEntity();

        if ($user instanceof User && $user->isClosed()) {
            $dateTimestamp = (new DateTime())->getTimestamp();
            $suffix = rand(0, 99999) + $dateTimestamp;
            $anonymousName = self::ANONYMOUS_PREFIX . $suffix;
            $user->setUsername($anonymousName);
            $user->setAvatar(null);
            $user->setEmail($anonymousName . '@dealabs-closed.com');
            $user->setPassword($this->encoder->encodePassword($user, $anonymousName . '@dealabs-closed.com'));
            foreach ($user->getSavedDeals() as $savedDeal) {
                $user->removeSavedDeal($savedDeal);
            }

            foreach ($user->getAlarms() as $alert) {
                $user->removeAlarm($alert);
            }
        }
    }
}
