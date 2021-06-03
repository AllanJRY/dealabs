<?php

namespace App\Twig;

use App\Repository\CategoryRepository;
use App\Repository\PartnerRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PartnerExtension extends AbstractExtension
{
    /**
     * @var CategoryRepository
     */
    private $partnerRepository;

    public function __construct(PartnerRepository $partnerRepository)
    {
        $this->partnerRepository = $partnerRepository;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_all_partner', [$this, 'getAllPartner']),
        ];
    }

    public function getAllPartner(): array
    {
        return $this->partnerRepository->findAll();
    }
}
