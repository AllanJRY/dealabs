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
            case 'Cobaye':
                return $this->computeCobayeBadgeProgress($badge, $user);
                break;
            default:
                return 0;
                break;
        }
    }

    public function computeCobayeBadgeProgress(Badge $badge, User $user) {
        return (count($user->getDeals())/10)*100;
    }
}
