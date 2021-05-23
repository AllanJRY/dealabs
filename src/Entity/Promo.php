<?php

namespace App\Entity;

use App\Repository\PromoRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PromoRepository::class)
 */
class Promo extends Deal
{
    /**
     * @ORM\ManyToOne(targetEntity=PromoType::class, inversedBy="promos")
     * @Assert\NotNull
     */
    private $promoType;

    public function getPromoType(): ?PromoType
    {
        return $this->promoType;
    }

    public function setPromoType(?PromoType $promoType): self
    {
        $this->promoType = $promoType;

        return $this;
    }
}
