<?php

namespace App\Twig;

use App\Entity\Badge;
use App\Entity\User;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BadgeExtension extends AbstractExtension
{

    public function getFunctions(): array
    {
        return [
            new TwigFunction('compute_progress', [$this, 'computeBadgeProgress']),
        ];
    }

    public function computeBadgeProgress(Badge $badge, User $user): int
    {
        switch ($badge->getTitle()) {
            case Badge::COBAYE_BADGE_TITLE:
                return $this->computeCobayeBadgeProgress($badge, $user);
            case Badge::SURVEILLANT_BADGE_TITLE:
                return $this->computeSurveillantBadgeProgress($badge, $user);
            case Badge::RAPPORT_STAGE_BADGE_TITLE:
                return $this->computeRapportStageBadgeProgress($badge, $user);
            default:
                return 0;
        }
    }

    public function computeCobayeBadgeProgress(Badge $badge, User $user)
    {
        return (count($user->getDeals()) / 10) * 100;
    }

    public function computeRapportStageBadgeProgress(Badge $badge, User $user)
    {
        return (count($user->getComments()) / 10) * 100;
    }

    public function computeSurveillantBadgeProgress(Badge $badge, User $user)
    {
        return (count($user->getRatings()) / 10) * 100;
    }
}
