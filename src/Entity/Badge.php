<?php

namespace App\Entity;

class Badge
{

    private $title;

    private $description;

    private $valid;

    private $value;

    public function __construct(string $title, string $description, int $valid, int $value)
    {
        $this->title = $title;
        $this->description = $description;
        $this->valid = $valid;
        $this->value = $value;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getValid(): ?int
    {
        return $this->valid;
    }

    public function setValid($valid): self
    {
        $this->valid = $valid;
        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue($value): self
    {
        $this->value = $value;
        return $this;
    }
}
