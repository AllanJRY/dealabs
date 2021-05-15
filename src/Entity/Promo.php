<?php

namespace App\Entity;

use App\Repository\PromoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PromoRepository::class)
 */
class Promo extends Deal
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=PromoType::class, inversedBy="promos")
     */
    private $promoType;

    public function getId(): ?int
    {
        return $this->id;
    }

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
