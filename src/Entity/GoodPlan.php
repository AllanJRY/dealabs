<?php

namespace App\Entity;

use App\Repository\GoodPlanRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=GoodPlanRepository::class)
 */
class GoodPlan extends Deal
{
    /**
     * @ORM\Column(type="float", nullable=true)
     * @Assert\PositiveOrZero()
     */
    private $price;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Assert\PositiveOrZero()
     */
    private $initialPrice;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Assert\PositiveOrZero()
     */
    private $shippingCost;

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getInitialPrice(): ?float
    {
        return $this->initialPrice;
    }

    public function setInitialPrice(?float $initialPrice): self
    {
        $this->initialPrice = $initialPrice;

        return $this;
    }

    public function getShippingCost(): ?float
    {
        return $this->shippingCost;
    }

    public function setShippingCost(?float $shippingCost): self
    {
        $this->shippingCost = $shippingCost;

        return $this;
    }
}
